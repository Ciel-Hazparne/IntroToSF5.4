<?php

namespace App\Entity;

class MeasureDateSearch
{
    private mixed $minDate;

    private mixed $maxDate;

    public function getMinDate(): ?\DateTimeInterface
    {
        return $this->minDate;
    }

    public function setMinDate(\DateTimeInterface $minDate): static
    {
        $this->minDate = $minDate;
        return $this;
    }

    public function getMaxDate(): ?\DateTimeInterface
    {
        return $this->maxDate;
    }

    public function setMaxDate(\DateTimeInterface $maxDate): static
    {
        $this->maxDate = $maxDate;
        return $this;
    }

}