<?php
    /* * * * * * * * * * * * * * * * * * * * * * *
     *
     *  uisysadmin / pt-pt
     *
     * * * * * * * * * * * * * * * * * * * * * * */

    return [
        /* * * * * * * * * * * * * * * * * * * * * GENERAL * * * * */
        'menutxt'       => 'Sysadmin',
        'modulename'    => 'Administração de sistema',
        /* * * * * * * * * * * * * * * * * * * * * PERMISSIONS * * * * */
        'perm1'         => 'Vista geral',
        /* * * * * * * * * * * * * * * * * * * * * UP MENUS * * * * */
        'menu1'         => 'Perfis',
        'menu2'         => 'Permissões',
        /* * * * * * * * * * * * * * * * * * * * * SEPARATORS * * * * */
        'sep1'          => 'Perfis do sistema',
        'sep2'          => 'Detalhes de perfil',
        'sep3'          => 'Criar novo perfil',
        'sep4'          => 'Alterar perfil',
        'sep5'          => 'Eliminar perfil',
        'sep6'          => 'Permissões de sistema',
        /* * * * * * * * * * * * * * * * * * * * * STRINGS * * * * */
        /* * * * * * * * * * * * * * * * * * * * * LABELS * * * * */
        'lbl1'          => 'Nome',
        'lbl2'          => 'Id',
        'lbl3'          => 'Descrição',
        /* * * * * * * * * * * * * * * * * * * * * BUTTONS * * * * */
        /* * * * * * * * * * * * * * * * * * * * * INFOS * * * * */
        'inf1'          => 'Novo perfil %1$s criado com sucesso.',
        'inf2'          => 'Perfil %1$s alterado com sucesso.',
        'inf3'          => 'Perfil %1$s eliminado com sucesso.',
        /* * * * * * * * * * * * * * * * * * * * * WARNINGS * * * * */
        'war1'          => 'Há utilizadores associados a este perfil. Alterá-lo pode afectar a maneira como utilizam a plataforma.<br>Altere com cuidado.',
        'war2'          => 'Há utilizadores associados a este perfil. Eliminá-lo pode afectar a maneira como utilizam a plataforma.<br>Prossiga com cuidado.',
        /* * * * * * * * * * * * * * * * * * * * * ERRORS * * * * */
        'err1'          => 'O nome do perfil não pode estar vazio.',
        'err2'          => 'O id não pode estar vazio.',
        'err3'          => 'O nome de perfil que escolheu já está a ser utilizado.',
        'err4'          => 'O Id que escolheu já está a ser utilizado.',
        /* * * * * * * * * * * * * * * * * * * * * LOGS * * * * */
        'log1'          => 'Criou o perfil "%1$s"',
        'log2'          => 'Alterou o perfil "%1$s"',
        'log3'          => 'Eliminou o perfil "%1$s"',
        'log4'          => 'Eliminou as permissões do sistema.',
        'log5'          => 'Adicionou novas permissões do sistema.',
        /* * * * * * * * * * * * * * * * * * * * * HELP * * * * */
        'help1'         => 'Nesta página listam-se os perfis de utilização do sistema. A cada um destes perfis estão associados diferentes conjuntos de permissões, que podem ser alterados no separador ao lado. A cada utilizador do sistema podem ser atribuídos estes perfis para acesso à plataforma.<br>Note-se que apenas utilizadores com permissões de sysadmin podem visualizar este menu.',
        'help2'         => 'Aqui pode ver detalhes do perfil "%1$s".<br>Para alterá-los, clique no botão.',
        'help3'         => 'Nesta página listam-se as permissões do sistema. Aqui pode dar e remover cada uma das permissões a cada um dos perfis.<br>Note-se que as permissões têm regras de precedência baseadas no bom-senso:<br>(sem permissão de vista geral, não poderá fazer nada mais, mesmo que com permissão para tal;<br>sem permissão para ver não poderá alterar; e<br>sem poder alterar não poderá eliminar.',
    
    ];
?>
