<?php

declare (strict_types=1);
namespace Ssch\TYPO3Rector\Helper;

use RectorPrefix20210603\Stringy\Stringy;
final class StringUtility
{
    public static function prepareExtensionName(string $extensionName, int $delimiterPosition) : string
    {
        $extensionName = \substr($extensionName, $delimiterPosition + 1);
        $stringy = new \RectorPrefix20210603\Stringy\Stringy($extensionName);
        $underScoredExtensionName = (string) $stringy->underscored()->toLowerCase()->humanize();
        $underScoredExtensionName = \ucwords($underScoredExtensionName);
        return \str_replace(' ', '', $underScoredExtensionName);
    }
}
