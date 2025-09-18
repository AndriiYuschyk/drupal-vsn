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

/* __string_template__3fcd64408771eeeebeab48d8d7574bc8 */
class __TwigTemplate_5cba45ae3f71cb23835d22c434a3ae7f extends Template
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
        yield "<a href=\"";
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, ($context["view_node"] ?? null), "html", null, true);
        yield "\" class=\"full-news-teaser\">
  <div class=\"common-news-teaser\">
    <div class=\"full-news-teaser__media\">
      ";
        // line 4
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, ($context["field_holovne_zobrazhennya"] ?? null), "html", null, true);
        yield "
";
        // line 5
        if ((($tmp = ($context["field_stiker"] ?? null)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 6
            yield "      <div class=\"full-news-teaser__stickers\">
          ";
            // line 7
            yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, ($context["field_stiker"] ?? null), "html", null, true);
            yield "
      </div>
";
        }
        // line 10
        yield "    </div>
    <div class=\"full-news-teaser__info\">
      <h3 class=\"full-news-teaser__title\">";
        // line 12
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, ($context["title"] ?? null), "html", null, true);
        yield "</h3>
        ";
        // line 13
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(($context["field_korotkyy_opys"] ?? null));
        yield "
      <span class=\"full-news-teaser__date\">";
        // line 14
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, ($context["created"] ?? null), "html", null, true);
        yield "</span>
    </div>
  </div>
</a>
";
        $this->env->getExtension('\Drupal\Core\Template\TwigExtension')
            ->checkDeprecations($context, ["view_node", "field_holovne_zobrazhennya", "field_stiker", "title", "field_korotkyy_opys", "created"]);        yield from [];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "__string_template__3fcd64408771eeeebeab48d8d7574bc8";
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
        return array (  78 => 14,  74 => 13,  70 => 12,  66 => 10,  60 => 7,  57 => 6,  55 => 5,  51 => 4,  44 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("", "__string_template__3fcd64408771eeeebeab48d8d7574bc8", "");
    }
    
    public function checkSecurity()
    {
        static $tags = ["if" => 5];
        static $filters = ["escape" => 1, "raw" => 13];
        static $functions = [];

        try {
            $this->sandbox->checkSecurity(
                ['if'],
                ['escape', 'raw'],
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
