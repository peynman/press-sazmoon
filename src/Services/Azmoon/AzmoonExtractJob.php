<?php

namespace Larapress\SAzmoon\Services\Azmoon;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Storage;
use Larapress\FileShare\Models\FileUpload;
use Larapress\Reports\Services\TaskScheduler\ITaskSchedulerService;
use ZipArchive;

class AzmoonExtractJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    /**
     * @var FileUpload
     */
    private $upload;
    private $uploadId;

    /**
     * Create a new job instance.
     *
     * @param FileUpload $message
     */
    public function __construct($uploadId)
    {
        $this->uploadId = $uploadId;
        $this->upload = FileUpload::find($uploadId);
        $this->onQueue(config('larapress.sazmoon.queue'));
    }

    public function tags()
    {
        return ['azmoon-extract', 'zipfile:' . $this->upload->id];
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        /** @var ITaskSchedulerService */
        $taskService = app(ITaskSchedulerService::class);

        $taskData = ['id' => $this->upload->id];
        $taskService->startSyncronizedTaskReport(
            AzmoonZipFileProcessor::class,
            'extract-azmoon-' . $this->upload->id,
            trans('larapress::sazmoon.file_processor_started'),
            $taskData,
            function ($onUpdate, $onSuccess, $onFailed) use ($taskData) {
                try {
                    $startTime = time();

                    $dir = substr($this->upload->path, 0, strrpos($this->upload->path, '.', -1));
                    Storage::disk($this->upload->storage)->makeDirectory($dir);

                    $stream = Storage::disk($this->upload->storage)->path($this->upload->path);
                    $zip = new ZipArchive();
                    $res = $zip->open($stream);
                    $data = [
                        'questions' => [],
                        'answers' => [],
                        'answer_sheet' => null,
                    ];
                    if ($res === true) {
                        for ($i = 0; $i < $zip->numFiles; $i++) {
                            $stat = $zip->statIndex($i);
                            if (\Illuminate\Support\Str::startsWith($stat['name'], "answers.txt")) {
                                $resource = $zip->getStream($zip->getNameIndex($i));
                                if ($resource && $stat['size'] < 16000000) { // 16mg
                                    if (!Storage::disk($this->upload->storage)->exists($dir.'/'.$stat['name'])) {
                                        Storage::disk($this->upload->storage)->writeStream($dir.'/'.$stat['name'], $resource);
                                    }
                                    $data['answer_sheet'] = $stat['name'];
                                }
                            } else {
                                if (\Illuminate\Support\Str::endsWith($stat['name'], ['jpeg', 'png', 'jpg'])) {
                                    if (\Illuminate\Support\Str::startsWith($stat['name'], ['q', 'a'])) {
                                        $filname = substr($stat['name'], 1, strpos($stat['name'], '.') - 1);

                                        if (is_numeric($filname)) {
                                            $resource = $zip->getStream($zip->getNameIndex($i));
                                            if ($resource && $stat['size'] <= 16000000) { // 16mg
                                                if (!Storage::disk($this->upload->storage)->exists($dir.'/'.$stat['name'])) {
                                                    Storage::disk($this->upload->storage)->writeStream($dir.'/'.$stat['name'], $resource);
                                                }
                                                if (\Illuminate\Support\Str::startsWith($stat['name'], 'q')) {
                                                    $data['questions'][] = $stat['name'];
                                                } else {
                                                    $data['answers'][] = $stat['name'];
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        $zip->close();
                    } else {
                        $onFailed(trans('larapress::sazmoon.file_processor_error'), $taskData);
                    }

                    $this->upload->update([
                        'data' => $data,
                    ]);
                    $took = time() - $startTime;
                    $onSuccess(trans('larapress::sazmoon.file_processor_finished', ['sec' => $took]), $taskData);
                } catch (\Exception $e) {
                    $onFailed('Error: '.$e->getMessage(), $taskData);
                }
            }
        );
    }
}
