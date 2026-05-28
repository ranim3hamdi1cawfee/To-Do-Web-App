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

/* task/base.html.twig */
class __TwigTemplate_4e489b13f22f29bceacb70b1e9cc8ede extends Template
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
            'title' => [$this, 'block_title'],
            'body' => [$this, 'block_body'],
        ];
    }

    protected function doDisplay(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "task/base.html.twig"));

        // line 1
        yield "<!DOCTYPE html>
<html lang=\"fr\">
<head>
    <meta charset=\"UTF-8\">
    <title>";
        // line 5
        yield from $this->unwrap()->yieldBlock('title', $context, $blocks);
        yield "</title>
    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: \x27Courier New\x27, monospace;
            background: #0a0a0a;
            color: #ccc;
            min-height: 100vh;
        }

        /* HEADER */
        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 18px 32px;
            border-bottom: 1px solid #1e1e1e;
        }
        .header-left { display: flex; align-items: center; gap: 14px; }
        .logo-box {
            background: #c8ff00;
            color: #000;
            font-weight: 900;
            font-size: 14px;
            width: 42px; height: 42px;
            display: flex; align-items: center; justify-content: center;
            border-radius: 8px;
        }
        .logo-title { font-size: 22px; font-weight: 700; color: #fff; }
        .logo-sub { font-size: 12px; color: #c8ff00; margin-top: 2px; }

        .header-right { display: flex; gap: 12px; }
        .badge-btn {
            display: flex; align-items: center; gap: 7px;
            border: 1px solid #333;
            border-radius: 20px;
            padding: 6px 16px;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 1px;
            color: #aaa;
            text-decoration: none;
        }
        .badge-btn .dot { width: 8px; height: 8px; border-radius: 50%; }
        .dot-green { background: #c8ff00; }
        .dot-red   { background: #ff4444; }

        /* LAYOUT */
        .layout {
            display: grid;
            grid-template-columns: 400px 1fr;
            min-height: calc(100vh - 73px);
        }

        /* SIDEBAR */
        .sidebar {
            border-right: 1px solid #1e1e1e;
            padding: 28px;
        }
        .sidebar-title {
            font-size: 18px;
            font-weight: 700;
            color: #fff;
            margin-bottom: 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .btn-create {
            background: #1a1a1a;
            border: 1px solid #333;
            color: #aaa;
            padding: 6px 14px;
            border-radius: 6px;
            font-size: 12px;
            font-family: \x27Courier New\x27, monospace;
            cursor: pointer;
            letter-spacing: 1px;
        }
        .btn-create:hover { border-color: #c8ff00; color: #c8ff00; }

        /* FORM */
        .field { margin-bottom: 20px; }
        .field label {
            display: block;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 1.5px;
            color: #555;
            margin-bottom: 8px;
            text-transform: uppercase;
        }
        .field input, .field select {
            width: 100%;
            background: #111;
            border: 1px solid #222;
            border-radius: 8px;
            padding: 12px 14px;
            color: #ccc;
            font-family: \x27Courier New\x27, monospace;
            font-size: 13px;
            transition: border-color 0.2s;
        }
        .field input::placeholder { color: #444; }
        .field input:focus, .field select:focus {
            outline: none;
            border-color: #c8ff00;
        }
        .field select option { background: #111; }

        /* PRIORITY BUTTONS */
        .priority-group { display: flex; gap: 10px; }
        .priority-btn {
            flex: 1;
            background: #111;
            border: 1px solid #222;
            border-radius: 8px;
            padding: 14px 10px;
            color: #555;
            font-family: \x27Courier New\x27, monospace;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 1px;
            cursor: pointer;
            text-align: center;
            transition: all 0.2s;
        }
        .priority-btn:hover { border-color: #c8ff00; color: #c8ff00; }
        .priority-btn .icon { font-size: 16px; display: block; margin-bottom: 6px; }

        .btn-submit {
            width: 100%;
            background: #c8ff00;
            color: #000;
            border: none;
            border-radius: 8px;
            padding: 14px;
            font-family: \x27Courier New\x27, monospace;
            font-size: 13px;
            font-weight: 900;
            letter-spacing: 2px;
            cursor: pointer;
            margin-top: 8px;
            transition: background 0.2s;
        }
        .btn-submit:hover { background: #b8ef00; }

        /* MAIN */
        .main { padding: 28px 32px; }
        .main-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }
        .main-title { font-size: 18px; font-weight: 700; color: #fff; }

        /* TASK LIST */
        .task-list { list-style: none; display: flex; flex-direction: column; gap: 10px; }
        .task-item {
            background: #111;
            border: 1px solid #1e1e1e;
            border-radius: 10px;
            padding: 16px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: border-color 0.2s;
        }
        .task-item:hover { border-color: #333; }
        .task-item.ff { border-left: 3px solid #ff4444; }
        .task-item.f  { border-left: 3px solid #f97316; }
        .task-item.mf { border-left: 3px solid #c8ff00; }
        .task-item.p  { border-left: 3px solid #34d399; }
        .task-item.pp { border-left: 3px solid #60a5fa; }

        .task-name { font-size: 14px; font-weight: 600; color: #e0e0e0; margin-bottom: 6px; }
        .task-meta { display: flex; gap: 8px; flex-wrap: wrap; }

        .pill {
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .5px;
            border: 1px solid #333;
            color: #777;
        }
        .pill-green { border-color: #c8ff0044; color: #c8ff00; background: #c8ff0011; }
        .pill-red   { border-color: #ff444444; color: #ff6666; background: #ff444411; }

        .tag {
            background: #1a1500;
            border: 1px solid #3a2f00;
            color: #c8a800;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 11px;
        }

        .btn-delete {
            background: none;
            border: 1px solid #2a2a2a;
            color: #555;
            padding: 5px 12px;
            border-radius: 6px;
            font-size: 12px;
            cursor: pointer;
            text-decoration: none;
            font-family: \x27Courier New\x27, monospace;
            transition: all 0.2s;
            white-space: nowrap;
        }
        .btn-delete:hover { border-color: #ff4444; color: #ff4444; }

        .empty {
            border: 1.5px dashed #1e1e1e;
            border-radius: 10px;
            padding: 80px 20px;
            text-align: center;
            color: #333;
            font-size: 14px;
        }

        .flash {
            background: #0d1f00;
            border: 1px solid #c8ff0033;
            color: #c8ff00;
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 13px;
        }
    </style>
</head>
<body>

<div class=\"header\">
    <div class=\"header-left\">
        <div class=\"logo-box\">TF</div>
        <div>
            <div class=\"logo-title\">TaskFlow</div>
            <div class=\"logo-sub\">● Planning Mode</div>
        </div>
    </div>
    <div class=\"header-right\">
        <span class=\"badge-btn\"><span class=\"dot dot-green\"></span> ACTIVE</span>
        <span class=\"badge-btn\"><span class=\"dot dot-red\"></span> URGENT</span>
    </div>
</div>

";
        // line 257
        yield from $this->unwrap()->yieldBlock('body', $context, $blocks);
        // line 258
        yield "
</body>
</html>";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        yield from [];
    }

    // line 5
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

    // line 257
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_body(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "body"));

        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        yield from [];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "task/base.html.twig";
    }

    /**
     * @codeCoverageIgnore
     */
    public function getDebugInfo(): array
    {
        return array (  337 => 257,  320 => 5,  310 => 258,  308 => 257,  53 => 5,  47 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("<!DOCTYPE html>
<html lang=\"fr\">
<head>
    <meta charset=\"UTF-8\">
    <title>{% block title %}TaskFlow{% endblock %}</title>
    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: \x27Courier New\x27, monospace;
            background: #0a0a0a;
            color: #ccc;
            min-height: 100vh;
        }

        /* HEADER */
        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 18px 32px;
            border-bottom: 1px solid #1e1e1e;
        }
        .header-left { display: flex; align-items: center; gap: 14px; }
        .logo-box {
            background: #c8ff00;
            color: #000;
            font-weight: 900;
            font-size: 14px;
            width: 42px; height: 42px;
            display: flex; align-items: center; justify-content: center;
            border-radius: 8px;
        }
        .logo-title { font-size: 22px; font-weight: 700; color: #fff; }
        .logo-sub { font-size: 12px; color: #c8ff00; margin-top: 2px; }

        .header-right { display: flex; gap: 12px; }
        .badge-btn {
            display: flex; align-items: center; gap: 7px;
            border: 1px solid #333;
            border-radius: 20px;
            padding: 6px 16px;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 1px;
            color: #aaa;
            text-decoration: none;
        }
        .badge-btn .dot { width: 8px; height: 8px; border-radius: 50%; }
        .dot-green { background: #c8ff00; }
        .dot-red   { background: #ff4444; }

        /* LAYOUT */
        .layout {
            display: grid;
            grid-template-columns: 400px 1fr;
            min-height: calc(100vh - 73px);
        }

        /* SIDEBAR */
        .sidebar {
            border-right: 1px solid #1e1e1e;
            padding: 28px;
        }
        .sidebar-title {
            font-size: 18px;
            font-weight: 700;
            color: #fff;
            margin-bottom: 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .btn-create {
            background: #1a1a1a;
            border: 1px solid #333;
            color: #aaa;
            padding: 6px 14px;
            border-radius: 6px;
            font-size: 12px;
            font-family: \x27Courier New\x27, monospace;
            cursor: pointer;
            letter-spacing: 1px;
        }
        .btn-create:hover { border-color: #c8ff00; color: #c8ff00; }

        /* FORM */
        .field { margin-bottom: 20px; }
        .field label {
            display: block;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 1.5px;
            color: #555;
            margin-bottom: 8px;
            text-transform: uppercase;
        }
        .field input, .field select {
            width: 100%;
            background: #111;
            border: 1px solid #222;
            border-radius: 8px;
            padding: 12px 14px;
            color: #ccc;
            font-family: \x27Courier New\x27, monospace;
            font-size: 13px;
            transition: border-color 0.2s;
        }
        .field input::placeholder { color: #444; }
        .field input:focus, .field select:focus {
            outline: none;
            border-color: #c8ff00;
        }
        .field select option { background: #111; }

        /* PRIORITY BUTTONS */
        .priority-group { display: flex; gap: 10px; }
        .priority-btn {
            flex: 1;
            background: #111;
            border: 1px solid #222;
            border-radius: 8px;
            padding: 14px 10px;
            color: #555;
            font-family: \x27Courier New\x27, monospace;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 1px;
            cursor: pointer;
            text-align: center;
            transition: all 0.2s;
        }
        .priority-btn:hover { border-color: #c8ff00; color: #c8ff00; }
        .priority-btn .icon { font-size: 16px; display: block; margin-bottom: 6px; }

        .btn-submit {
            width: 100%;
            background: #c8ff00;
            color: #000;
            border: none;
            border-radius: 8px;
            padding: 14px;
            font-family: \x27Courier New\x27, monospace;
            font-size: 13px;
            font-weight: 900;
            letter-spacing: 2px;
            cursor: pointer;
            margin-top: 8px;
            transition: background 0.2s;
        }
        .btn-submit:hover { background: #b8ef00; }

        /* MAIN */
        .main { padding: 28px 32px; }
        .main-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }
        .main-title { font-size: 18px; font-weight: 700; color: #fff; }

        /* TASK LIST */
        .task-list { list-style: none; display: flex; flex-direction: column; gap: 10px; }
        .task-item {
            background: #111;
            border: 1px solid #1e1e1e;
            border-radius: 10px;
            padding: 16px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: border-color 0.2s;
        }
        .task-item:hover { border-color: #333; }
        .task-item.ff { border-left: 3px solid #ff4444; }
        .task-item.f  { border-left: 3px solid #f97316; }
        .task-item.mf { border-left: 3px solid #c8ff00; }
        .task-item.p  { border-left: 3px solid #34d399; }
        .task-item.pp { border-left: 3px solid #60a5fa; }

        .task-name { font-size: 14px; font-weight: 600; color: #e0e0e0; margin-bottom: 6px; }
        .task-meta { display: flex; gap: 8px; flex-wrap: wrap; }

        .pill {
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .5px;
            border: 1px solid #333;
            color: #777;
        }
        .pill-green { border-color: #c8ff0044; color: #c8ff00; background: #c8ff0011; }
        .pill-red   { border-color: #ff444444; color: #ff6666; background: #ff444411; }

        .tag {
            background: #1a1500;
            border: 1px solid #3a2f00;
            color: #c8a800;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 11px;
        }

        .btn-delete {
            background: none;
            border: 1px solid #2a2a2a;
            color: #555;
            padding: 5px 12px;
            border-radius: 6px;
            font-size: 12px;
            cursor: pointer;
            text-decoration: none;
            font-family: \x27Courier New\x27, monospace;
            transition: all 0.2s;
            white-space: nowrap;
        }
        .btn-delete:hover { border-color: #ff4444; color: #ff4444; }

        .empty {
            border: 1.5px dashed #1e1e1e;
            border-radius: 10px;
            padding: 80px 20px;
            text-align: center;
            color: #333;
            font-size: 14px;
        }

        .flash {
            background: #0d1f00;
            border: 1px solid #c8ff0033;
            color: #c8ff00;
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 13px;
        }
    </style>
</head>
<body>

<div class=\"header\">
    <div class=\"header-left\">
        <div class=\"logo-box\">TF</div>
        <div>
            <div class=\"logo-title\">TaskFlow</div>
            <div class=\"logo-sub\">● Planning Mode</div>
        </div>
    </div>
    <div class=\"header-right\">
        <span class=\"badge-btn\"><span class=\"dot dot-green\"></span> ACTIVE</span>
        <span class=\"badge-btn\"><span class=\"dot dot-red\"></span> URGENT</span>
    </div>
</div>

{% block body %}{% endblock %}

</body>
</html>", "task/base.html.twig", "C:\\Users\\Lenovo\\Desktop\\project_Symfony\\mon_projet\\templates\\task\\base.html.twig");
    }
}
