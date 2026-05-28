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

/* task/index.html.twig */
class __TwigTemplate_16a21d7f60f352146b999c406852e314 extends Template
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

        $this->blocks = [
            'title' => [$this, 'block_title'],
            'body' => [$this, 'block_body'],
        ];
    }

    protected function doGetParent(array $context): bool|string|Template|TemplateWrapper
    {
        // line 1
        return "base.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "task/index.html.twig"));

        $this->parent = $this->load("base.html.twig", 1);
        yield from $this->parent->unwrap()->yield($context, array_merge($this->blocks, $blocks));
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

    }

    // line 2
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_title(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "title"));

        yield "TaskFlow";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        yield from [];
    }

    // line 4
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_body(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "body"));

        // line 5
        yield "<div class=\"layout\">

    ";
        // line 8
        yield "    <div class=\"sidebar\">
        <div class=\"sidebar-title\">
            New Task
            <a href=\"";
        // line 11
        yield $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("task_add");
        yield "\" class=\"btn-create\">+CREATE</a>
        </div>
        <p style=\"color:#444; font-size:13px; margin-top: 40px; text-align:center;\">
            Clique sur +CREATE<br>pour ajouter une tâche
        </p>
    </div>

    ";
        // line 19
        yield "    <div class=\"main\">

        ";
        // line 21
        $context['_parent'] = $context;
        $context['_seq'] = CoreExtension::ensureTraversable(CoreExtension::getAttribute($this->env, $this->source, (isset($context["app"]) || array_key_exists("app", $context) ? $context["app"] : (function () { throw new RuntimeError('Variable "app" does not exist.', 21, $this->source); })()), "flashes", ["success"], "method", false, false, false, 21));
        foreach ($context['_seq'] as $context["_key"] => $context["message"]) {
            // line 22
            yield "            <div class=\"flash\">✓ ";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($context["message"], "html", null, true);
            yield "</div>
        ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_key'], $context['message'], $context['_parent']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 24
        yield "
        <div class=\"main-header\">
            <div class=\"main-title\">All Tasks</div>
            <span style=\"color:#444; font-size:12px; letter-spacing:1px;\">LIST</span>
        </div>

        ";
        // line 30
        if (Twig\Extension\CoreExtension::testEmpty((isset($context["tasks"]) || array_key_exists("tasks", $context) ? $context["tasks"] : (function () { throw new RuntimeError('Variable "tasks" does not exist.', 30, $this->source); })()))) {
            // line 31
            yield "            <div class=\"empty\">No tasks yet. Create one →</div>
        ";
        } else {
            // line 33
            yield "            <ul class=\"task-list\">
                ";
            // line 34
            $context['_parent'] = $context;
            $context['_seq'] = CoreExtension::ensureTraversable(Twig\Extension\CoreExtension::reverse($this->env->getCharset(), (isset($context["tasks"]) || array_key_exists("tasks", $context) ? $context["tasks"] : (function () { throw new RuntimeError('Variable "tasks" does not exist.', 34, $this->source); })())));
            foreach ($context['_seq'] as $context["_key"] => $context["task"]) {
                // line 35
                yield "                    <li class=\"task-item ";
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["task"], "priority", [], "any", false, false, false, 35), "html", null, true);
                yield "\">
                        <div>
                            <div class=\"task-name\">";
                // line 37
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["task"], "title", [], "any", false, false, false, 37), "html", null, true);
                yield "</div>
                            <div class=\"task-meta\">
                                <span class=\"pill pill-green\">";
                // line 39
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(Twig\Extension\CoreExtension::upper($this->env->getCharset(), CoreExtension::getAttribute($this->env, $this->source, $context["task"], "priority", [], "any", false, false, false, 39)), "html", null, true);
                yield "</span>
                                <span class=\"pill\">";
                // line 40
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["task"], "movement", [], "any", false, false, false, 40), "html", null, true);
                yield "</span>
                                ";
                // line 41
                $context['_parent'] = $context;
                $context['_seq'] = CoreExtension::ensureTraversable(CoreExtension::getAttribute($this->env, $this->source, $context["task"], "tags", [], "any", false, false, false, 41));
                foreach ($context['_seq'] as $context["_key"] => $context["tag"]) {
                    // line 42
                    yield "                                    <span class=\"tag\">";
                    yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($context["tag"], "html", null, true);
                    yield "</span>
                                ";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_key'], $context['tag'], $context['_parent']);
                $context = array_intersect_key($context, $_parent) + $_parent;
                // line 44
                yield "                                ";
                if ((($tmp = CoreExtension::getAttribute($this->env, $this->source, $context["task"], "dueDate", [], "any", false, false, false, 44)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
                    // line 45
                    yield "                                    <span class=\"pill\">📅 ";
                    yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["task"], "dueDate", [], "any", false, false, false, 45), "html", null, true);
                    yield "</span>
                                ";
                }
                // line 47
                yield "                            </div>
                        </div>
                        <a href=\"";
                // line 49
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("task_delete", ["id" => CoreExtension::getAttribute($this->env, $this->source, $context["task"], "id", [], "any", false, false, false, 49)]), "html", null, true);
                yield "\"
                           class=\"btn-delete\"
                           onclick=\"return confirm(\x27Supprimer ?\x27)\">✕</a>
                    </li>
                ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_key'], $context['task'], $context['_parent']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 54
            yield "            </ul>
        ";
        }
        // line 56
        yield "    </div>

</div>
";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        yield from [];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "task/index.html.twig";
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
        return array (  202 => 56,  198 => 54,  187 => 49,  183 => 47,  177 => 45,  174 => 44,  165 => 42,  161 => 41,  157 => 40,  153 => 39,  148 => 37,  142 => 35,  138 => 34,  135 => 33,  131 => 31,  129 => 30,  121 => 24,  112 => 22,  108 => 21,  104 => 19,  94 => 11,  89 => 8,  85 => 5,  75 => 4,  58 => 2,  41 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("{% extends \x27base.html.twig\x27 %}
{% block title %}TaskFlow{% endblock %}

{% block body %}
<div class=\"layout\">

    {# SIDEBAR - formulaire rapide #}
    <div class=\"sidebar\">
        <div class=\"sidebar-title\">
            New Task
            <a href=\"{{ path(\x27task_add\x27) }}\" class=\"btn-create\">+CREATE</a>
        </div>
        <p style=\"color:#444; font-size:13px; margin-top: 40px; text-align:center;\">
            Clique sur +CREATE<br>pour ajouter une tâche
        </p>
    </div>

    {# MAIN - liste #}
    <div class=\"main\">

        {% for message in app.flashes(\x27success\x27) %}
            <div class=\"flash\">✓ {{ message }}</div>
        {% endfor %}

        <div class=\"main-header\">
            <div class=\"main-title\">All Tasks</div>
            <span style=\"color:#444; font-size:12px; letter-spacing:1px;\">LIST</span>
        </div>

        {% if tasks is empty %}
            <div class=\"empty\">No tasks yet. Create one →</div>
        {% else %}
            <ul class=\"task-list\">
                {% for task in tasks|reverse %}
                    <li class=\"task-item {{ task.priority }}\">
                        <div>
                            <div class=\"task-name\">{{ task.title }}</div>
                            <div class=\"task-meta\">
                                <span class=\"pill pill-green\">{{ task.priority|upper }}</span>
                                <span class=\"pill\">{{ task.movement }}</span>
                                {% for tag in task.tags %}
                                    <span class=\"tag\">{{ tag }}</span>
                                {% endfor %}
                                {% if task.dueDate %}
                                    <span class=\"pill\">📅 {{ task.dueDate }}</span>
                                {% endif %}
                            </div>
                        </div>
                        <a href=\"{{ path(\x27task_delete\x27, {id: task.id}) }}\"
                           class=\"btn-delete\"
                           onclick=\"return confirm(\x27Supprimer ?\x27)\">✕</a>
                    </li>
                {% endfor %}
            </ul>
        {% endif %}
    </div>

</div>
{% endblock %}", "task/index.html.twig", "C:\\Users\\Lenovo\\Desktop\\project_Symfony\\mon_projet\\templates\\task\\index.html.twig");
    }
}
