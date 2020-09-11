<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Extension\SandboxExtension;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Source;
use Twig\Template;

/* __string_template__f68ee6bd4ec3b2909406a90e4cef7bba6b4b5bddc3c524b2de92849ab9829a45 */
class __TwigTemplate_03ee7641511ca1c1b6d32dc91ac59694a538f06e4e49c756d6af43edd9f2d5d1 extends Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
        ];
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 1
        echo "<p>Hi, my name is ";
        echo twig_escape_filter($this->env, ($context["name"] ?? null), "html", null, true);
        echo ".</p>";
    }

    public function getTemplateName()
    {
        return "__string_template__f68ee6bd4ec3b2909406a90e4cef7bba6b4b5bddc3c524b2de92849ab9829a45";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "__string_template__f68ee6bd4ec3b2909406a90e4cef7bba6b4b5bddc3c524b2de92849ab9829a45", "");
    }
}
