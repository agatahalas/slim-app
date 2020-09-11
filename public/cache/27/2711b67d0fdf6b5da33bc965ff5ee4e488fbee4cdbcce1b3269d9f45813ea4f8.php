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

/* __string_template__4afffba584304db1d88c1799e3b0db5398c771e88d5e59375627b552001d56d5 */
class __TwigTemplate_489eca27f03ef3a53a25d7d091a1a2cacb8be587b369ff3fa79d274c0a4bfd7e extends Template
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
        echo "<h1>Hi, my name is ";
        echo twig_escape_filter($this->env, ($context["name"] ?? null), "html", null, true);
        echo ".</h1>";
    }

    public function getTemplateName()
    {
        return "__string_template__4afffba584304db1d88c1799e3b0db5398c771e88d5e59375627b552001d56d5";
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
        return new Source("", "__string_template__4afffba584304db1d88c1799e3b0db5398c771e88d5e59375627b552001d56d5", "");
    }
}
