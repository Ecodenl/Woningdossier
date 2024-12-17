@foreach($questionnaires as $questionnaire)
    @php
        $file = $fileType->files()->mostRecent($questionnaire)->first();

        $questionnaireForFileType = $fileType
            ->files()
            ->where('questionnaire_id', $questionnaire->id)
            ->first();

        $questionnaireForFileTypeExists = $questionnaireForFileType instanceof \App\Models\FileStorage;
        $questionnaireBeingProcessed = $fileType->isQuestionnaireBeingProcessed($questionnaire);

        $anonymizedText = stristr($fileType->short, 'anonymized') ? 'zonder adresgegevens' : 'met adresgegevens';
        $fileName = "Vragenlijst | {$questionnaire->name}, {$anonymizedText}";

        if ($questionnaire->isNotActive()) {
            $fileName .= " (INACTIEF)";
        }
    @endphp
    <tr>
        <td>
            {{$fileName}}
            @if($questionnaireForFileType instanceof \App\Models\FileStorage && ! $questionnaireBeingProcessed)
                <a class="in-text block" href="{{route('cooperation.file-storage.download', ['fileStorage' => $questionnaireForFileType])}}">
                    {{$fileName}}
                    ({{$questionnaireForFileType->created_at->format('Y-m-d H:i')}})
                </a>
            @endif
        </td>

        <td>
            @if($questionnaireBeingProcessed)
                <div title="@lang('woningdossier.cooperation.admin.cooperation.reports.index.table.report-in-queue')">
                    <button class="btn btn-green flex items-center" type="button" disabled>
                        @lang('cooperation/frontend/tool.my-plan.downloads.create-report')
                        <i class="icon-sm icon-ventilation-fan animate-spin-slow ml-1"></i>
                    </button>
                </div>
            @else
                <form action="{{route('cooperation.file-storage.store', ['fileType' => $fileType->short])}}"
                      method="POST">
                    @csrf

                    <input type="hidden" name="file_storages[questionnaire_id]"
                           value="{{$questionnaire->id}}">

                    <button class="btn btn-green" type="submit">
                        @lang('cooperation/frontend/tool.my-plan.downloads.create-report')
                    </button>
                </form>
            @endif
        </td>
    </tr>
@endforeach