<?php

namespace Consola\Command;

interface Command
{
    public function setUp();
    public function setDescription($description);
    public function setSignature($signature);
}
