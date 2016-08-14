<?php

namespace Documentor\src\Application\Views;

use Documentor\src\Application\Models\Comment;
use Reflection;

class ClassView extends DocView
{
    public function getTop() : string
    {
        $abstract = $this->ref->isAbstract() ? 'abstract ' : '';
        $type     = $this->ref->isInterface() ? 'interface ' : 'class ';
        $type     = $this->ref->isTrait() ? 'trait ' : $type;
        $name     = $this->ref->getShortName() . ' ';
        $extends  = $this->ref->getParentClass() !== false ? 'extends ' . $this->linkType($this->ref->getParentClass()->getName()) . ' ' : '';

        $interfaces = $this->ref->getInterfaces();
        $implements = '';

        foreach ($interfaces as $interface) {
            $implements .= $this->linkType($interface->getName(), $interface->getShortName()) . ', ';
        }

        $implements = $implements !== '' ? 'implements ' . rtrim($implements, ', ') : '';

        return trim($this->formatClassType($abstract . $type) . $name . $extends . $implements);
    }

    public function getMembers()
    {
        $members = $this->ref->getProperties();
        foreach ($members as $member) {
            $type = new Comment($member->getDocComment());
            yield "    " . $this->formatModifier(implode(' ', Reflection::getModifierNames($member->getModifiers()))) . ' ' . $this->linkType($type->getVar()) . ' ' . $this->formatVariable($member->getName()) . ';';
        }
    }

    public function getMethods()
    {
        $methods = $this->ref->getMethods();
        foreach ($methods as $method) {
            $parameters      = $method->getParameters();
            $parameterString = '';

            foreach ($parameters as $parameter) {
                $parameterString .= ($parameter->hasType() ? $this->linkType($parameter->getType()) . ' ' : '')
                    . ($parameter->isPassedByReference() ? '&' : '')
                    . $this->formatVariable($parameter->getName())
                    . ($parameter->isDefaultValueAvailable() ? ' = '
                        . ($parameter->isDefaultValueConstant() ? $parameter->getDefaultValueConstantName() : is_array($parameter->getDefaultValue()) ? '[...]' : is_null($parameter->getDefaultValue()) ? 'null' : $parameter->getDefaultValue()) : '')
                    . ', ';
            }

            $methodString = "    " . $this->formatModifier(implode(' ', Reflection::getModifierNames($method->getModifiers())))
            . ' ' . $this->formatFunction() . ' ' . $method->getShortName()
            . '(' . trim($parameterString, ', ') . ')'
            . ($method->hasReturnType() ? ' : ' . $this->linkType($method->getReturnType()) : '');

            yield $methodString;
        }
    }

    private function formatClassType(string $type) : string
    {
        return '<span class="classType">' . $type . '</span>';
    }
}