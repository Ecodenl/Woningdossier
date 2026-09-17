<?php

namespace App\Console\Commands\Api\SmartTwin;

use App\Enums\MappingType;
use App\Helpers\Hoomdossier;
use App\Models\MeasureApplication;
use App\Services\MappingService;
use App\Services\SmartTwin\Api\SmartTwinApi;
use Illuminate\Console\Command;

/**
 * Couples SmartTwin's catalogue of solutions to our measure applications.
 *
 * Reads the whole catalogue rather than waiting for a product to turn up unmapped in somebody's
 * advice, and writes a coupling per solution: one product, one measure.
 *
 * The kind of measure in a solution id is how the catalogue is read here, because the products of
 * one kind are usually the same measure to us and deciding them together is far less work. That is
 * a convenience of this command, not something the lookup relies on — see SolutionMeasures.
 *
 * It does not hold everywhere. "Replace Boiler" alone spans a boiler and three heat pumps, which
 * are four different measures here, so those kinds are listed under PER_PRODUCT and left alone
 * until somebody couples their products individually.
 *
 * Temporary. Once the coupling screen exists it owns the couplings, and this keeps only the
 * catalogue import — otherwise a re-run would silently undo manual work.
 *
 * Run with --dry-run first: it prints the catalogue grouped by kind, with the solution ids, and
 * says of every kind whether it is coupled, needs per-product work, has no counterpart here, or is
 * simply unknown.
 */
class SyncSolutions extends Command
{
    protected $signature = 'api:smarttwin:sync-solutions {--dry-run}';

    protected $description = 'Couple the SmartTwin solution catalogue to our measure applications.';

    /**
     * Kinds where every product is the same measure to us.
     *
     * The names line up almost word for word, which is what makes these safe: "Insulate Facade
     * Inside" against Binnengevelisolatie, "Insulate Flat Roof On Top" against "Plat dak isoleren op
     * dakbedekking".
     */
    private const KINDS = [
        'Insulate Facade Cavity'       => 'cavity-wall-insulation',          // Spouwmuurisolatie
        'Insulate Facade Inside'       => 'facade-wall-insulation',          // Binnengevelisolatie
        'Insulate Ground Floor'        => 'floor-insulation',                // Vloerisolatie
        'Insulate Crawlspace Floor'    => 'bottom-insulation',               // Bodemisolatie
        'Insulate Pitched Roof Inside' => 'roof-insulation-pitched-inside',  // Schuin dak isoleren van binnenuit
        'Insulate Flat Roof On Top'    => 'roof-insulation-flat-current',    // Plat dak isoleren op dakbedekking
        'Seal Cracks & Seams'          => 'crack-sealing',                   // Kierdichting verbeteren
        'Install Solar Panels'         => 'solar-panels-place-replace',      // Zonnepanelen plaatsen/vervangen

        // Insulating a roof from the outside means taking the covering off first, which is what
        // separates these two from the ones above. Reads right, not yet confirmed by anyone who
        // knows the measures.
        'Insulate Pitched Roof Outside' => 'roof-insulation-pitched-replace-tiles', // + dakpannen vervangen
        'Insulate Flat Roof Outside'    => 'roof-insulation-flat-replace-current',  // + dakbedekking vervangen
    ];

    /**
     * Kinds that hold products of several different measures. A row on the kind would be wrong for
     * most of them, so they wait for rows on their full solution ids.
     *
     * @var array<string, string>  kind => why
     */
    private const PER_PRODUCT = [
        'Replace Boiler'      => 'een HR107-ketel en drie warmtepompen zijn vier maatregelen bij ons',
        'Install Ventilation' => 'wij kennen gebalanceerde, decentrale en vraaggestuurde ventilatie apart',
        'Replace Glass'       => 'de glassoort bepaalt of het HR++ of HR+++ wordt',
        'Replace Window'      => 'glassoort en kozijntype bepalen samen welke maatregel het is',
    ];

    /**
     * Kinds SmartTwin advises that Hoomdossier has no measure for. Listed so they stop showing up
     * as an open decision, and so it is written down that the gap was seen rather than missed.
     *
     * @var array<string, string>  kind => why
     */
    private const NO_COUNTERPART = [
        'Install Heating Network'    => 'warmtenet-aansluiting bestaat hier niet als maatregel',
        'Install Sun Blinds'         => 'zonwering bestaat hier niet als maatregel',
        'Replace Radiator'           => 'afgiftesysteem is bij ons een vraag (building-heating-application), geen maatregel',
        'Replace Stove'              => 'kooktoestel is bij ons een vraag (cook-type), geen maatregel',
        'Replace Collective Heating' => 'collectieve installaties bestaan hier niet als maatregel',

        // Neither of these has an obvious counterpart: we have no exterior facade insulation at all,
        // and our two flat roof measures are both about the covering rather than the inside.
        'Insulate Facade Outside'   => 'wij kennen alleen spouw- en binnengevelisolatie',
        'Insulate Flat Roof Inside' => 'onze platdak-maatregelen gaan allebei over de dakbedekking',
    ];

    /**
     * A solution id reads as `<kind of measure>|<provider>:<product>`. Grouping on that is a
     * convenience for whoever does the coupling, not something the lookup relies on — see
     * SolutionMeasures.
     */
    private const PROVIDER_SEPARATOR = '|';

    private function kindOf(string $solutionId): ?string
    {
        if (! str_contains($solutionId, self::PROVIDER_SEPARATOR)) {
            return null;
        }

        return trim(explode(self::PROVIDER_SEPARATOR, $solutionId, 2)[0]);
    }

    public function handle(SmartTwinApi $api): int
    {
        if (! Hoomdossier::hasEnabledSmartTwinCalls()) {
            $this->error('SmartTwin calls are disabled, set SMARTTWIN_ENABLED and SMARTTWIN_KEY.');

            return self::FAILURE;
        }

        $dryRun = (bool) $this->option('dry-run');

        if ($dryRun) {
            $this->info('DRY-RUN mode: no changes will be made.');
        }

        $solutions = $api->advice()->getAllSolutions()['solutions'] ?? [];

        if (empty($solutions)) {
            $this->error('The catalogue came back empty, aborting without changing anything.');

            return self::FAILURE;
        }

        $this->info(count($solutions) . ' solutions in the catalogue.');
        $this->newLine();

        $products = [];

        foreach ($solutions as $solution) {
            $id = $solution['id'] ?? '';
            $kind = $this->kindOf($id) ?? '(geen provider in het id)';
            $products[$kind][] = ['id' => $id, 'name' => $solution['name'] ?? $id];
        }

        ksort($products);

        $coupled = 0;
        $openDecisions = 0;

        foreach ($products as $kind => $items) {
            $count = count($items);

            if (isset(self::KINDS[$kind])) {
                $measure = MeasureApplication::findByShort(self::KINDS[$kind]);

                if (! $measure instanceof MeasureApplication) {
                    $this->error("Measure application '" . self::KINDS[$kind] . "' not found, aborting without changing anything.");

                    return self::FAILURE;
                }

                $this->line(sprintf('<info>%-30s</info> -> %-38s (%d)', $kind, $measure->short, $count));

                // A row per product, not per kind. The kind is how the catalogue is read here; what
                // the lookup resolves is the solution id itself, so that is what gets written.
                if (! $dryRun) {
                    foreach ($items as $item) {
                        MappingService::init()
                            ->from($item['id'])
                            ->sync([$measure], MappingType::SMARTTWIN_SOLUTION_MEASURE_APPLICATION->value);
                    }
                }

                ++$coupled;

                continue;
            }

            if (isset(self::NO_COUNTERPART[$kind])) {
                $this->line(sprintf('<comment>%-30s</comment> -- geen maatregel: %s (%d)', $kind, self::NO_COUNTERPART[$kind], $count));

                continue;
            }

            // Everything left needs a decision, and the ids are what somebody couples on.
            $reason = self::PER_PRODUCT[$kind] ?? 'onbekend soort, nog niet beoordeeld';
            $this->warn(sprintf('%-30s ?? %s (%d)', $kind, $reason, $count));

            foreach ($items as $item) {
                $this->line("      {$item['name']}");
                $this->line("        <fg=gray>{$item['id']}</>");
            }

            ++$openDecisions;
        }

        $this->newLine();
        $this->info(sprintf(
            '%d kind(s) %s, %d kind(s) without a counterpart, %d still to decide.',
            $coupled,
            $dryRun ? 'would be coupled' : 'coupled',
            count(self::NO_COUNTERPART),
            $openDecisions,
        ));

        if ($openDecisions > 0) {
            $this->warn('Solutions of an undecided kind reach no woonplan; they are reported as VALUE_UNMAPPED.');
        }

        return self::SUCCESS;
    }
}
