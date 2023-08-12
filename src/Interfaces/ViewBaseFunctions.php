<?php

namespace Komodo\Routes\Interfaces;

/*
|-----------------------------------------------------------------------------
| Komodo Routes
|-----------------------------------------------------------------------------
|
| Desenvolvido por: Jhonnata Paixão (Líder de Projeto)
| Iniciado em: 15/10/2022
| Arquivo: ViewBaseFunctions.php
| Data da Criação Fri Jul 21 2023
| Copyright (c) 2023
|
|-----------------------------------------------------------------------------
|*/

use Komodo\Routes\Response;

trait ViewBaseFunctions
{
    /**
     * @var string|null
     */
    protected $template;

    /**
     * @var string
     */
    protected $view;

    /**
     * @param Response $response
     * @param string $view
     * @param array $variables
     *
     * @return string|bool
     */
    public function renderView($view, $variables = [])
    {
        /*
        |-----------------------------------------------------------------------------
        | Extraí e declare as váviaveis usadas pela view
        |-----------------------------------------------------------------------------
        |   Todas as variáveis que estiverem sendo referênciadas
        |   na view, devem estar declaradas de forma explícita aqui
        |   antes da geração do html
        |*/
        extract($variables);

        /*
        |-----------------------------------------------------------------------------
        | Geração do HTML
        |-----------------------------------------------------------------------------
        |   Neste procedimento é obtido através do fluxo
        |   a string correspondente ao html já montado pela view
        |   e depois enviar através do response para o cliente
        |*/
        ob_start();
        include $view;
        $html = ob_get_clean();
        
        /*
        |-----------------------------------------------------------------------------
        | Envio do html montado
        |-----------------------------------------------------------------------------
        |   Após a etapa anterior, todo o conteúdo do html já montado é
        |   armazenado na $html e será renderizado agora usando o
        |   $response->write e ->send
        |*/

        return $html;
    }

    public function sendView($view)
    {
        $vars = is_array($this->body) ? $this->body : [];

        if ($this->template) {
            $html = $this->renderView($this->template, $vars);
            $html = preg_replace('/<Outlet[^>]*>/', $this->renderView($view, $vars), $html);
        } else {
            $html = $this->renderView($view, $vars);
        }
        $this->write($html)->send();
    }

    /**
     * @param string $template
     *
     * @return $this
     */
    public function setTemplate($template)
    {
        $this->template = $template;
        return $this;
    }
}
