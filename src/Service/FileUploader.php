<?php
    namespace App\Service;

    use Symfony\Component\HttpFoundation\File\Exception\FileException;
    use Symfony\Component\HttpFoundation\File\UploadedFile;

    class FileUploader
    {
        private $targetDirectory;
        private $baseUrl;

        public function __construct($targetDirectory, $baseUrl)
        {
            $this->targetDirectory = $targetDirectory;
            $this->baseUrl = $baseUrl;
        }

        public function upload(String $file)
        {
            //$originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            //$safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
            $fileName = uniqid().'.jpeg';
            $path = $this->getTargetDirectory().'/'.$fileName;
            $fullPath = $this->getBaseUrl().'/'.$fileName;


            try {
                $directory = fopen($path, "wb");
                $data = explode(',', $file);
                fwrite($directory, base64_decode($data[1]));
                fclose($directory);

                return $fullPath;
            } catch (FileException $e) {
                return $e;
            }
        }

        /**
         * @return mixed
         */
        public function getTargetDirectory()
        {
            return $this->targetDirectory;
        }

        /**
         * @return mixed
         */
        public function getBaseUrl()
        {
            return $this->baseUrl;
        }
    }