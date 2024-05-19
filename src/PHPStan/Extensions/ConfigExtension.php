<?php

namespace Devdojo\Auth\PHPStan\Extensions;

use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Type\DynamicMethodReturnTypeExtension;
use PHPStan\Type\ObjectType;
use PHPStan\Type\Type;

class ConfigExtension implements DynamicMethodReturnTypeExtension
{
    public function getClass(): string
    {
        return \Illuminate\Support\Facades\Config::class;
    }

    public function isMethodSupported(MethodReflection $methodReflection): bool
    {
        return $methodReflection->getName() === 'write';
    }

    public function getTypeFromMethodCall(
        MethodReflection $methodReflection,
        \PhpParser\Node\Expr\MethodCall $methodCall,
        Scope $scope
    ): Type {
        return new ObjectType(\Devdojo\ConfigWriter\Repository::class);
    }
}
