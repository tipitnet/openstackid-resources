<?php namespace App\Http\Utils;
/**
 * Copyright 2017 OpenStack Foundation
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * http://www.apache.org/licenses/LICENSE-2.0
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 **/
use App\Services\Model\IFolderService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use models\main\File;
/**
 * Class FileUploader
 * @package App\Http\Utils
 */
final class FileUploader implements IFileUploader
{
    /**
     * @var IFolderService
     */
    private $folder_service;

    /**
     * @var IBucket
     */
    private $bucket;

    /**
     * FileUploader constructor.
     * @param IFolderService $folder_service
     * @param IBucket $bucket
     */
    public function __construct(IFolderService $folder_service, IBucket $bucket){
        $this->folder_service = $folder_service;
        $this->bucket = $bucket;
    }

    /**
     * @param UploadedFile $file
     * @param $folder_name
     * @param bool $is_image
     * @return File
     * @throws \Exception
     */
    public function build(UploadedFile $file, $folder_name, $is_image = false){
        $attachment = new File();
        try {

            $client_original_name = $file->getClientOriginalName();
            Log::debug(sprintf("FileUploader::build: folder_name %s client original name %s", $folder_name, $client_original_name));

            $local_path = Storage::putFileAs(sprintf('/public/%s', $folder_name), $file, $client_original_name);

            Log::debug(sprintf("FileUploader::build: saved to local path %s", $local_path));

            Log::debug(sprintf("FileUploader::build: invoking folder service findOrMake folder_name %s", $folder_name));
            $folder = $this->folder_service->findOrMake($folder_name);
            $local_path = Storage::disk()->path($local_path);
            $attachment->setParent($folder);
            $attachment->setName($client_original_name);
            $file_name = sprintf("assets/%s/%s", $folder_name, $client_original_name);
            Log::debug(sprintf("FileUploader::build file_name %s", $file_name));
            $title = str_replace(array('-', '_'), ' ', preg_replace('/\.[^.]+$/', '', $file->getClientOriginalName()));
            $attachment->setFilename($file_name);
            Log::debug(sprintf("FileUploader::build title %s", $title));
            $attachment->setTitle($title);
            $attachment->setShowInSearch(true);
            if ($is_image) // set className
                $attachment->setImage();
            Log::debug(sprintf("FileUploader::build uploading to bucket %s", $local_path));
            $this->bucket->put($attachment, $local_path);
            $attachment->setCloudMeta('LastPut', time());
            $attachment->setCloudStatus('Live');
            $attachment->setCloudSize(filesize($local_path));

        }
        catch (\Exception $ex){
            Log::error($ex);
            throw $ex;
        }
        return $attachment;
    }
}