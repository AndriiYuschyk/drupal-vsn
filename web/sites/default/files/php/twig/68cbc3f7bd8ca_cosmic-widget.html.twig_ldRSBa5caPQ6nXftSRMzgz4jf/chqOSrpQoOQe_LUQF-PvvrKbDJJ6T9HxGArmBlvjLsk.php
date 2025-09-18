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

/* modules/custom/cosmic_widget/templates/cosmic-widget.html.twig */
class __TwigTemplate_2547a55ecd43fabee4ed04a9f5d8a63b extends Template
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
        yield "<div class=\"cosmic-widget\" id=\"cosmic-widget\">
    <div class=\"cosmic-widget__header\">
        <h2 class=\"cosmic-widget__title\">🚀 ";
        // line 3
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("Cosmic Widget"));
        yield "</h2>
        <div class=\"cosmic-widget__controls\">
            <button type=\"button\" class=\"cosmic-widget__btn\" id=\"cosmic-refresh-btn\">
                <span class=\"cosmic-widget__btn-icon\">🪐</span>
                ";
        // line 7
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("Get New Image"));
        yield "
            </button>
        </div>
    </div>

    <div class=\"cosmic-widget__content\">
        ";
        // line 13
        if ((($tmp = ($context["image_url"] ?? null)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 14
            yield "            <div class=\"cosmic-widget__image-container\">
                <img src=\"";
            // line 15
            yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, ($context["image_url"] ?? null), "html", null, true);
            yield "\" alt=\"";
            yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, ($context["image_title"] ?? null), "html", null, true);
            yield "\" class=\"cosmic-widget__image\" id=\"cosmic-image\" loading=\"lazy\">
                <div class=\"cosmic-widget__image-overlay\">
                    <div class=\"cosmic-widget__image-info\">
                        <h3 class=\"cosmic-widget__image-title\">";
            // line 18
            yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, ($context["image_title"] ?? null), "html", null, true);
            yield "</h3>
                        <p class=\"cosmic-widget__image-date\">";
            // line 19
            yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->extensions['Twig\Extension\CoreExtension']->formatDate(($context["date"] ?? null), "d.m.Y"), "html", null, true);
            yield "</p>
                    </div>
                </div>
            </div>
        ";
        }
        // line 24
        yield "
        <div class=\"cosmic-widget__details\">
            ";
        // line 26
        if ((($tmp = ($context["image_explanation"] ?? null)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 27
            yield "                <div class=\"cosmic-widget__description\">
                    <h4>✨ ";
            // line 28
            yield $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("About this image"));
            yield ":</h4>
                    <div class=\"cosmic-widget__text-slider\">
                        <div class=\"cosmic-widget__text-content\" data-full-text=\"";
            // line 30
            yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, ($context["image_explanation"] ?? null), "html", null, true);
            yield "\">
                            ";
            // line 31
            yield (((Twig\Extension\CoreExtension::length($this->env->getCharset(), ($context["image_explanation"] ?? null)) > 200)) ? ($this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, (Twig\Extension\CoreExtension::slice($this->env->getCharset(), ($context["image_explanation"] ?? null), 0, 200) . "..."), "html", null, true)) : ($this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, ($context["image_explanation"] ?? null), "html", null, true)));
            yield "
                        </div>
                        ";
            // line 33
            if ((Twig\Extension\CoreExtension::length($this->env->getCharset(), ($context["image_explanation"] ?? null)) > 200)) {
                // line 34
                yield "                            <div class=\"cosmic-widget__slider-controls\">
                                <button type=\"button\" class=\"cosmic-widget__slider-btn\" id=\"text-prev\">‹</button>
                                <div class=\"cosmic-widget__slider-info\">
                                    <span id=\"text-current\">1</span> / <span id=\"text-total\">2</span>
                                </div>
                                <button type=\"button\" class=\"cosmic-widget__slider-btn\" id=\"text-next\">›</button>
                            </div>
                        ";
            }
            // line 42
            yield "                    </div>
                </div>
            ";
        }
        // line 45
        yield "
            ";
        // line 46
        if ((($tmp = ($context["fact"] ?? null)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 47
            yield "                <div class=\"cosmic-widget__fact\">
                    <h4>🌟 ";
            // line 48
            yield $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("Did you know?"));
            yield "</h4>
                    <p>";
            // line 49
            yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, ($context["fact"] ?? null), "html", null, true);
            yield "</p>
                </div>
            ";
        }
        // line 52
        yield "        </div>
    </div>
</div>";
        $this->env->getExtension('\Drupal\Core\Template\TwigExtension')
            ->checkDeprecations($context, ["image_url", "image_title", "date", "image_explanation", "fact"]);        yield from [];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "modules/custom/cosmic_widget/templates/cosmic-widget.html.twig";
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
        return array (  147 => 52,  141 => 49,  137 => 48,  134 => 47,  132 => 46,  129 => 45,  124 => 42,  114 => 34,  112 => 33,  107 => 31,  103 => 30,  98 => 28,  95 => 27,  93 => 26,  89 => 24,  81 => 19,  77 => 18,  69 => 15,  66 => 14,  64 => 13,  55 => 7,  48 => 3,  44 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("", "modules/custom/cosmic_widget/templates/cosmic-widget.html.twig", "/opt/drupal/web/modules/custom/cosmic_widget/templates/cosmic-widget.html.twig");
    }
    
    public function checkSecurity()
    {
        static $tags = ["if" => 13];
        static $filters = ["t" => 3, "escape" => 15, "date" => 19, "length" => 31, "slice" => 31];
        static $functions = [];

        try {
            $this->sandbox->checkSecurity(
                ['if'],
                ['t', 'escape', 'date', 'length', 'slice'],
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
