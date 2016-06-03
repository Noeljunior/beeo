<?php
    /* * * * * * * * * * * * * * * * * * * * * * *
     *
     *  uipatients / pt-pt
     *
     * * * * * * * * * * * * * * * * * * * * * * */

    $MASCULINO = TRUE;
    $SINGULAR = 'paciente';
    $PLURAL = 'pacientes';
    $__xdefpluart    = $MASCULINO ? 'os' : 'as';
    $__xdefsingart   = $MASCULINO ? 'o' : 'a';
    $__xundefsingart = $MASCULINO ? 'um' : 'uma';

    return [
        /* * * * * * * * * * * * * * * * * * * * * GERAIS * * * * */
        'menutxt'       => ucfirst($PLURAL),
        'modulename'    => 'Gestão de '.$PLURAL,
        'name'          => $PLURAL,
        /* * * * * * * * * * * * * * * * * * * * * PERMISSIONS * * */
        'perm1'         => 'Vista geral',
        'perm2'         => 'Procurar '.$PLURAL.' por identificador',
        'perm3'         => 'Criar '.$PLURAL,
        'perm4'         => 'Ver lista com tod'.$__xdefsingart.'s '.$__xdefsingart.'s '.$PLURAL,
        'perm5'         => 'Ver detalhes de '.$__xundefsingart.' '.$SINGULAR.' existente',
        'perm6'         => 'Alterar informação sobre '.$__xundefsingart.' '.$SINGULAR.' existente',
        'perm7'         => 'Pode alterar o identificador dos '.$PLURAL,
        /* * * * * * * * * * * * * * * * * * * * * UP MENUS * * * * */
        /* * * * * * * * * * * * * * * * * * * * * SEPARATORS * * * */
        'sep1'          => 'Procurar '.$PLURAL,
        'sep2'          => 'Lista de todos os '.$PLURAL,
        'sep3'          => 'Ficha d'.$__xdefsingart.' '.$SINGULAR,
        //'sep4'          => 'Informação d'.$__xdefsingart.' '.$SINGULAR,

        'lbl1'          => 'Identificador do '.$SINGULAR,
        'lbl2'          => 'Data em que foi adicionad'.$__xdefsingart,
        'lbl3'          => ucfirst($SINGULAR).' adicionad'.$__xdefsingart.' por',

        'sep5'          => 'Histórico d'.$__xdefsingart.' '.$SINGULAR,
        'sep6'          => 'Alterar '.$SINGULAR,
        'sep7'          => 'Conteúdo do evento',

        'sepne1'        => 'Novo evento',
        'lblne1'        => 'Nome do evento',
        'lblne2'        => 'Data em que aconteceu',
        'lblne3'        => 'Hora em que aconteceu',
        'lblne4'        => 'Tipo de evento',

        'sepve1'        => 'Detalhes sobre o evento realizado',
        'lblve1'        => 'Nome do evento',
        'lblve2'        => 'Data de realização',
        'lblve3'        => 'Criado por',
        'lblve4'        => 'Tipo de evento',

        'sepna1'        => 'Nova actividade',
        'lblna1'        => 'Nome da actividade',
        'lblna2'        => 'Data em que aconteceu',
        'lblna3'        => 'Hora em que aconteceu',
        'lblna4'        => 'Tipo de actividade',
        'sepna2'        => 'Questões',

        'sepva1'        => 'Detalhes da actividade efectuada',
        'lblva1'        => 'Nome da actividade',
        'lblva2'        => 'Data em que aconteceu',
        'lblva3'        => 'Criada por',
        'lblva4'        => 'Tipo de actividade',
        'sepva2'        => 'Respostas dadas',

        /* * * * * * * * * * * * * * * * * * * * * STRINGS * * * * */
        /* * * * * * * * * * * * * * * * * * * * * LABELS * * * * */
        /* * * * * * * * * * * * * * * * * * * * * BUTTONS * * * * */
        'btn1'          => 'Ver '.$SINGULAR,
        'btn2'          => 'Submeter respostas',
        /* * * * * * * * * * * * * * * * * * * * * INFOS * * * * */
        'inf1'          => 'Nov'.$__xdefsingart.' '.$SINGULAR.' \'%1$s\' criad'.$__xdefsingart.' com sucesso.',
        //'inf2'          => ucfirst($SINGULAR).' \'%1$s\' alterado com sucesso.',
        'inf3'          => 'Novo evento %2$s criado n'.$__xdefsingart.' '.$SINGULAR.' %1$s com sucesso.',
        'inf4'          => 'Nova actividade %2$s criada n'.$__xdefsingart.' '.$SINGULAR.' %1$s com sucesso.',
        /* * * * * * * * * * * * * * * * * * * * * WARNINGS * * * * */
        'war1'          => ucfirst($__xdefsingart).' '.$SINGULAR.' que tentou procurar não existe. Deseja criá-l'.$__xdefsingart.'?',
        //'war2'          => ($MASCULINO ? 'Este ' : 'Esta ').$SINGULAR.' tem histórico associado na plataforma. Alterá-l'.$__xdefsingart.' pode deturpar o significado do seu histórico.<br>Altere com cuidado.<br>Não é possível eliminá-l'.$__xdefsingart.' enquanto tiver histórico associado.',
        'war3'          => ucfirst($__xdefsingart).' '.$SINGULAR.' que está a tentar procurar não existe.',
        'war4'          => 'Por favor introduza o identificador d'.$__xundefsingart.' '.$SINGULAR,
        /* * * * * * * * * * * * * * * * * * * * * ERRORS * * * * */
        'err1'          => 'O nome d'.$__xdefsingart.' '.$SINGULAR.' não pode estar vazio.',
        'err2'          => 'O nome d'.$__xdefsingart.' '.$SINGULAR.' que escolheu já está a ser utilizado.',
        /* * * * * * * * * * * * * * * * * * * * * LOGS * * * * */
        'log1'          => 'Criou '.$__xdefsingart.' '.$SINGULAR.' \'%1$s\'',
        //'log2'          => 'Alterou '.$__xdefsingart.' '.$SINGULAR.' \'%1$s\'',
        //'log3'          => 'Viu informações sobre '.$__xdefsingart.' '.$SINGULAR.' \'%1$s\'',
        //'log4'          => 'Eliminou '.$__xdefsingart.' '.$SINGULAR.' \'%1$s\'',
        'log5'          => 'Criou uma acção %2$s n'.$__xdefsingart.' '.$SINGULAR.' \'%1$s\'',
        'log6'          => 'Criou um evento %2$s n'.$__xdefsingart.' '.$SINGULAR.' \'%1$s\'',
        /* * * * * * * * * * * * * * * * * * * * * HELP * * * * */
    
    ];
?>
