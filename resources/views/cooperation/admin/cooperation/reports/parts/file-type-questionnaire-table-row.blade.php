@foreach($questionnaires as $questionnaire)
    <?php
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
    ?>
    <tr>
        <td>{{$fileName}}
            <ul>
                @if($questionnaireForFileType instanceof \App\Models\FileStorage && !$questionnaireBeingProcessed)
                    <li>
                        <a @if(!$questionnaireBeingProcessed)
                           href="{{route('cooperation.file-storage.download', ['fileStorage' => $questionnaireForFileType])}}" @endif>
                            {{$fileName}}
                            ({{$questionnaireForFileType->created_at->format('Y-m-d H:i')}})
                        </a>
                    </li>
                @endif
            </ul>
        </td>

        <td>
            <form action="{{route('cooperation.file-storage.store', ['fileType' => $fileType->short])}}"
                  method="post">
                <input type="hidden" name="file_storages[questionnaire_id]"
                       value="{{$questionnaire->id}}">
                @csrf
                <button
                        @if($questionnaireBeingProcessed) disabled="disabled"
                        type="button" data-toggle="tooltip"
                        title="@lang('woningdossier.cooperation.admin.cooperation.reports.index.table.report-in-queue')"
                        @else
                        type="submit"
                        @endif
                        class="btn btn-{{$questionnaireBeingProcessed ? 'warning' : 'primary'}}"
                >
                    @lang('my-plan.download.title')
                    @if($questionnaireBeingProcessed)
                        <span class="glyphicon glyphicon-repeat fast-right-spinner"></span>
                    @endif
                </button>
            </form>
        </td>
    </tr>
@endforeach