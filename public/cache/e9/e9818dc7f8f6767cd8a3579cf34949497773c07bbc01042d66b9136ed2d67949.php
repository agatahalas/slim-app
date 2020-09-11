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

/* base.html.twig */
class __TwigTemplate_d740c4cee375764e7891faffcb28f915e139d2f20a07997f892ad12b2506ad83 extends Template
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
        echo "<!DOCTYPE html>
<html lang=\"en\">
  <head>
    ";
        // line 4
        ob_start(function () { return ''; });
        // line 5
        echo "      ";
        $this->loadTemplate("partials/head.html.twig", "base.html.twig", 5)->display($context);
        // line 6
        echo "    ";
        $context["head"] = ('' === $tmp = ob_get_clean()) ? '' : new Markup($tmp, $this->env->getCharset());
        // line 7
        echo "  </head>

  <body>
    ";
        // line 10
        ob_start(function () { return ''; });
        // line 11
        echo "      ";
        $this->loadTemplate("partials/body.html.twig", "base.html.twig", 11)->display($context);
        // line 12
        echo "    ";
        $context["content"] = ('' === $tmp = ob_get_clean()) ? '' : new Markup($tmp, $this->env->getCharset());
        // line 13
        echo "
    ";
        // line 14
        ob_start(function () { return ''; });
        // line 15
        echo "      ";
        $this->loadTemplate("partials/footer.html.twig", "base.html.twig", 15)->display($context);
        // line 16
        echo "    ";
        $context["footer"] = ('' === $tmp = ob_get_clean()) ? '' : new Markup($tmp, $this->env->getCharset());
        // line 17
        echo "  </body>
</html>
";
    }

    public function getTemplateName()
    {
        return "base.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  74 => 17,  71 => 16,  68 => 15,  66 => 14,  63 => 13,  60 => 12,  57 => 11,  55 => 10,  50 => 7,  47 => 6,  44 => 5,  42 => 4,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "base.html.twig", "/app/app/templates/base.html.twig");
    }
}
