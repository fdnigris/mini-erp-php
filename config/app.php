<?php

// Ajusta o relógio pro nosso fuso (SP/BR)
date_default_timezone_set('America/Sao_Paulo');

// Define o idioma pra português
setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'portuguese');

// Formata números e moeda no padrão BR
setlocale(LC_MONETARY, 'pt_BR', 'pt_BR.utf-8', 'portuguese'); 