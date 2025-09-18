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

/* @better_exposed_filters/bef-nested-elements.html.twig */
class __TwigTemplate_cf86f6c4a6796c8378cd1504963342bd extends Template
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
        // line 15
        $context["delta"] = abs((($context["current_nesting_level"] ?? null) - ($context["new_nesting_level"] ?? null)));
        // line 16
        if ((($tmp = CoreExtension::getAttribute($this->env, $this->source, ($context["loop"] ?? null), "first", [], "any", false, false, true, 16)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 17
            yield "  <ul>
";
        } else {
            // line 19
            yield "  ";
            if ((($tmp = ($context["delta"] ?? null)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
                // line 20
                yield "    ";
                $context['_parent'] = $context;
                $context['_seq'] = CoreExtension::ensureTraversable(range(1, ($context["delta"] ?? null)));
                foreach ($context['_seq'] as $context["_key"] => $context["i"]) {
                    // line 21
                    yield "      ";
                    if ((($context["new_nesting_level"] ?? null) > ($context["current_nesting_level"] ?? null))) {
                        // line 22
                        yield "        <ul>
      ";
                    } else {
                        // line 24
                        yield "        </ul>
      ";
                    }
                    // line 26
                    yield "    ";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_key'], $context['i'], $context['_parent']);
                $context = array_intersect_key($context, $_parent) + $_parent;
                // line 27
                yield "  ";
            }
        }
        // line 29
        yield "
<li>";
        // line 30
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, ($context["item"] ?? null), "html", null, true);
        yield "

";
        // line 32
        if ((($tmp = CoreExtension::getAttribute($this->env, $this->source, ($context["loop"] ?? null), "last", [], "any", false, false, true, 32)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 33
            yield "  ";
            // line 34
            yield "  ";
            $context['_parent'] = $context;
            $context['_seq'] = CoreExtension::ensureTraversable(range(($context["new_nesting_level"] ?? null), 0));
            foreach ($context['_seq'] as $context["_key"] => $context["i"]) {
                // line 35
                yield "    </li></ul>
  ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_key'], $context['i'], $context['_parent']);
            $context = array_intersect_key($context, $_parent) + $_parent;
        }
        $this->env->getExtension('\Drupal\Core\Template\TwigExtension')
            ->checkDeprecations($context, ["current_nesting_level", "new_nesting_level", "loop", "item"]);        yield from [];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "@better_exposed_filters/bef-nested-elements.html.twig";
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
        return array (  98 => 35,  93 => 34,  91 => 33,  89 => 32,  84 => 30,  81 => 29,  77 => 27,  71 => 26,  67 => 24,  63 => 22,  60 => 21,  55 => 20,  52 => 19,  48 => 17,  46 => 16,  44 => 15,);
    }

    public function getSourceContext(): Source
    {
        return new Source("", "@better_exposed_filters/bef-nested-elements.html.twig", "/opt/drupal/web/modules/contrib/better_exposed_filters/templates/bef-nested-elements.html.twig");
    }
    
    public function checkSecurity()
    {
        static $tags = ["set" => 15, "if" => 16, "for" => 20];
        static $filters = ["abs" => 15, "escape" => 30];
        static $functions = ["range" => 20];

        try {
            $this->sandbox->checkSecurity(
                ['set', 'if', 'for'],
                ['abs', 'escape'],
                ['range'],
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
