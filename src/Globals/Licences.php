<?php

namespace App\Globals;


use App\Repository\LicenceRepository;

class Licences
{
    private $licenceRepository;

    public function __construct(LicenceRepository $licenceRepository)
    {
        $this->licenceRepository = $licenceRepository;
    }

    public function getAll()
    {
        $licences = $this->licenceRepository->findAll();

        return $licences;
    }

}
