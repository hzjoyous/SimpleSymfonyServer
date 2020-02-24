<?php

declare(strict_types=1);

namespace App\Controller;

trait Response
{

    public function responseData($data)
    {
        return $this->json([
            'status'  => 0,
            'message' => '',
            'value'   => $data
        ]);
    }

    public function responeDataBuild($data)
    {
        return [
            'status'  => 0,
            'message' => '',
            'value'   => $data
        ];
    }
}
