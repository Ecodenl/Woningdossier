<?php

namespace App\Http\Controllers\Cooperation\Admin\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Cooperation;
use App\Models\LanguageLine;
use App\Models\Step;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class TranslationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $steps = Step::where('short', '!=', 'general-data')->get();

        $mailLangFiles = [
            'cooperation/mail/account-associated-with-cooperation' => '(mail) | Account gekoppeld met coöperatie',
            'cooperation/mail/account-created' => '(mail) | Account aangemaakt',
            'cooperation/mail/changed-email' => '(mail) | Email aangepast',
            'cooperation/mail/confirm-account' => '(mail) | Bevestig account',
            'cooperation/mail/reset-password' => '(mail) | Reset wachtwoord',
            'cooperation/mail/unread-message-count' => '(mail) | Ongelezen berichten',
        ];

        return view('cooperation.admin.super-admin.translations.index', compact('steps', 'mailLangFiles'));
    }

    /**
     *
     * @param  \App\Models\Cooperation  $cooperation
     * @param  string  $group  So we can get the translations / questions from language_line table for the step
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Cooperation $cooperation, string $group)
    {
        // see the index file, we change the "/" to "_" otherwise it won't be picked up by routing

        $group = str_replace('_', '/', $group);
        // it is what it is, for the time being this will do. TODO: should be refactored
        $step = Step::findByShort($group);

        if ($step instanceof Step && ! is_null($step->parent_id)) {
            $group = "cooperation/tool/general-data/{$group}";
        }
        if ('ventilation' == $group) {
            $group = "cooperation/tool/{$group}";
        }

        if ('pdf-user-report' == $group) {
            $group = 'pdf/user-report';
        }

        $translations = LanguageLine::with([
            'subQuestions' => function ($query) {
                return $query->with('helpText');
            }, 'helpText', ])
            ->forGroup($group)
            ->mainQuestions()
            ->get();

        return view('cooperation.admin.super-admin.translations.edit', compact('translations', 'group'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Cooperation  $cooperation
     * @param $group
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Cooperation $cooperation, $group)
    {
        $languageLinesData = $request->get('language_lines', []);

        foreach ($languageLinesData as $locale => $languageLineData) {
            foreach ($languageLineData as $type => $languageLines) {
                // we don't do stuff with the type yet, could be helpful in the future.
                foreach ($languageLines as $languageLineId => $text) {
                    $text = $text ?? '';
                    $languageLine = LanguageLine::find($languageLineId);
                    if ($languageLine instanceof LanguageLine) {
                        $languageLine->setTranslation($locale, $text);
                        $languageLine->save();
                    }
                }
            }
        }

        Artisan::call('queue:restart');

        return redirect()
            ->route('cooperation.admin.super-admin.translations.index')
            ->with('success', __('woningdossier.cooperation.admin.super-admin.translations.update.success'));
    }
}
