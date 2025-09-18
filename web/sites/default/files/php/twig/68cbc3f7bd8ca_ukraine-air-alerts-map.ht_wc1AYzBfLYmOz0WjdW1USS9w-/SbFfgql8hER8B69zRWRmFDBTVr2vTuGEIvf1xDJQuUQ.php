<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Extension\CoreExtension;
use Twig\Extension\SandboxExtension;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Source;
use Twig\Template;
use Twig\TemplateWrapper;

/* modules/custom/ukraine_air_alerts/templates/ukraine-air-alerts-map.html.twig */
class __TwigTemplate_2fcca1a2a97ab527d178c36bfe5359fd extends Template
{
    private Source $source;
    /**
     * @var array<string, Template>
     */
    private array $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
        ];
        $this->sandbox = $this->extensions[SandboxExtension::class];
        $this->checkSecurity();
    }

    protected function doDisplay(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        // line 1
        yield "<div id=\"ukraine-air-alerts-map\"";
        if ((($tmp = ($context["compact_mode"] ?? null)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            yield " class=\"compact-mode\"";
        }
        yield ">
    <div class=\"map-container\">
        ";
        // line 3
        if ((($tmp =  !($context["compact_mode"] ?? null)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 4
            yield "            <div class=\"map-header\">
                <h2>
                    ";
            // line 6
            yield $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("Air Raid Alerts in Ukraine"));
            yield "
                    <img src=\"https://cdn.alerts.in.ua/assets/icons/ukraine.png\"
                         alt=\"Ukraine\"
                         class=\"map-header-flag\">
                </h2>
                <div class=\"last-updated\">
                    ";
            // line 12
            yield $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("Last updated:"));
            yield " <span id=\"last-updated-time\">";
            yield $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("Loading..."));
            yield "</span>
                </div>
            </div>
        ";
        }
        // line 16
        yield "
        ";
        // line 17
        if ((($tmp = ($context["show_legend"] ?? null)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 18
            yield "            <div class=\"map-legend\">
                <div class=\"legend-item\">
                    <span class=\"legend-color legend-none\"></span>
                    <span class=\"legend-text\">";
            // line 21
            yield $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("No Alert"));
            yield "</span>
                </div>
                <div class=\"legend-item\">
                    <span class=\"legend-color legend-partial\"></span>
                    <span class=\"legend-text\">";
            // line 25
            yield $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("Partial Alert"));
            yield "</span>
                </div>
                <div class=\"legend-item\">
                    <span class=\"legend-color legend-full\"></span>
                    <span class=\"legend-text\">";
            // line 29
            yield $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("Full Alert"));
            yield "</span>
                </div>
            </div>
        ";
        }
        // line 33
        yield "
        <div class=\"ukraine-map\">
            <svg id=\"ukraine-map-svg\" viewBox=\"0 0 1000 670\" xmlns=\"http://www.w3.org/2000/svg\" aria-label=\"";
        // line 35
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("Air Raid Alerts in Ukraine"));
        yield "\">
                <!-- SVG paths of Ukrainian regions will be injected dynamically via JavaScript -->
            </svg>
        </div>

        ";
        // line 40
        if ((($tmp = ($context["show_status"] ?? null)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 41
            yield "            <div class=\"map-status\">
                <div id=\"connection-status\" class=\"status-connected\">
                    <span class=\"status-indicator\"></span>
                    <span class=\"status-text\">";
            // line 44
            yield $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("Connected"));
            yield "</span>
                </div>
                <div class=\"refresh-info\">
                    ";
            // line 47
            yield $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("Next update in:"));
            yield " <span id=\"countdown\">--</span> ";
            yield $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("sec"));
            yield "
                </div>
            </div>
        ";
        }
        // line 51
        yield "    </div>
</div>
";
        $this->env->getExtension('\Drupal\Core\Template\TwigExtension')
            ->checkDeprecations($context, ["compact_mode", "show_legend", "show_status"]);        yield from [];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "modules/custom/ukraine_air_alerts/templates/ukraine-air-alerts-map.html.twig";
    }

    /**
     * @codeCoverageIgnore
     */
    public function isTraitable(): bool
    {
        return false;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getDebugInfo(): array
    {
        return array (  141 => 51,  132 => 47,  126 => 44,  121 => 41,  119 => 40,  111 => 35,  107 => 33,  100 => 29,  93 => 25,  86 => 21,  81 => 18,  79 => 17,  76 => 16,  67 => 12,  58 => 6,  54 => 4,  52 => 3,  44 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("", "modules/custom/ukraine_air_alerts/templates/ukraine-air-alerts-map.html.twig", "/opt/drupal/web/modules/custom/ukraine_air_alerts/templates/ukraine-air-alerts-map.html.twig");
    }
    
    public function checkSecurity()
    {
        static $tags = ["if" => 1];
        static $filters = ["t" => 6];
        static $functions = [];

        try {
            $this->sandbox->checkSecurity(
                ['if'],
                ['t'],
                [],
                $this->source
            );
        } catch (SecurityError $e) {
            $e->setSourceContext($this->source);

            if ($e instanceof SecurityNotAllowedTagError && isset($tags[$e->getTagName()])) {
                $e->setTemplateLine($tags[$e->getTagName()]);
            } elseif ($e instanceof SecurityNotAllowedFilterError && isset($filters[$e->getFilterName()])) {
                $e->setTemplateLine($filters[$e->getFilterName()]);
            } elseif ($e instanceof SecurityNotAllowedFunctionError && isset($functions[$e->getFunctionName()])) {
                $e->setTemplateLine($functions[$e->getFunctionName()]);
            }

            throw $e;
        }

    }
}
