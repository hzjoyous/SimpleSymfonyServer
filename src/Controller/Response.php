<?php

declare(strict_types=1);

namespace App\Controller;

trait Response
{

    public function responseData($data)
    {
        return $this->json([
            'code'  => 0,
            'errMsg' => '',
            'data'   => $data
        ]);
    }

    public function responseDataBuild($data)
    {
        return [
            'code'  => 0,
            'errMsg' => '',
            'data'   => $data
        ];
    }
}
