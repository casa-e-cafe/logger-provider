<?php
/**
 * Created by Rafael Girolineto
 * User: Rafael
 * Date: 18/12/17
 */

namespace CasaCafe\Library\Logger\Processor;

interface SensitiveInfoProcessorInterface
{
    public function __construct(array $config = []);

    public function replaceSensitiveInfo($info);

}
