<?php

namespace Consola;

interface Command
{
    public function handle();
    public function setUp();
    public function setDescription($description);
    public function setSignature($signature);
}
