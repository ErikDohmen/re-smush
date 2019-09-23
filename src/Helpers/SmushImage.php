<?php

namespace OffbeatWP\ReSmush\Helpers;

use \Exception;

class SmushImage
{

    public function __construct($image)
    {
        $this->image = $image;
        $this->quality = 90;
        $this->url = 'http://api.resmush.it/?qlty=';
        $this->exif = 'true';
    }

    public function execute()
    {
        if ($this->hasAllowedType($this->image['type']) == true && $this->hasAllowedSize($this->image['file']) == true) {
            $request = $this->makeCurlRequest($this->image['file']);

            if ($request != false) {
                $this->pullImage($request, $this->image['file']);
            }
        }
    }

    public function setExif($exif)
    {
        if ($exif == true) {
            $this->exif = 'true';
        } else {
            $this->exif = 'false';
        }
    }

    public function hasAllowedType($image)
    {
        switch ($image) {
            case "image/jpeg":
                return true;
                break;
            case "image/png":
                return true;
                break;
            case "image/gif":
                return true;
                break;
            default:
                return false;
                break;
        }
    }

    public function hasAllowedSize($image)
    {
        if (filesize($image) < 5242880) {
            return true;
        }

        return false;
    }

    public function setUrl($url)
    {
        $this->url = $url;
    }

    public function setQuality($quality)
    {
        $this->quality = $quality;
    }

    public function pullImage($result, $file)
    {
        $content = file_get_contents($result->dest);
        file_put_contents($file, $content);
    }

    public function makeCurlRequest($file)
    {
        $mime = mime_content_type($file);
        $info = pathinfo($file);
        $name = $info['basename'];
        $output = new \CURLFile($file, $mime, $name);

        $data = [
            "files" => $output,
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url . $this->quality . '&exif=' . $this->exif);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        $result = curl_exec($ch);

        if (curl_errno($ch)) {
            error_log(curl_error($ch));

            return false;
        }

        curl_close($ch);

        try {
            $this->hasNoCurlError($result);
        } catch (Exception $e) {
            error_log($e->getMessage());

            return false;
        }

        return json_decode($result);
    }

    public function hasNoCurlError($result)
    {
        $checkError = json_decode($result);

        if (isset($checkError->error)) {
            switch ($checkError->error) {
                case 301:

                    throw new Exception('error Log:' . $checkError->error_long);

                    break;
                case 400:
                    throw new Exception('error Log:' . $checkError->error_long);

                    break;
                case 402:
                    throw new Exception('error Log:' . $checkError->error_long);

                    break;
                case 403:
                    throw new Exception('error Log:' . $checkError->error_long);

                    break;
                case 404:
                    throw new Exception('error Log:' . $checkError->error_long);

                    break;
                case 501:
                    throw new Exception('error Log:' . $checkError->error_long);

                    break;
                case 502:
                    throw new Exception('error Log:' . $checkError->error_long);

                    break;
                case 503:
                    throw new Exception('error Log:' . $checkError->error_long);

                    break;
                case 504:
                    throw new Exception('error Log:' . $checkError->error_long);

                    break;
            }
        }

        return true;
    }

}