<?php
declare(strict_types=1);

namespace galaxygames\ovommand\fetus;

use galaxygames\ovommand\BaseCommand;
use galaxygames\ovommand\BaseSubCommand;
use galaxygames\ovommand\enum\HardEnum;

class IdealSubCMD extends BaseSubCommand{
    public function prepare() : void{
        $this->registerArgument(new , new HardEnum($name, $values));
    }
}
