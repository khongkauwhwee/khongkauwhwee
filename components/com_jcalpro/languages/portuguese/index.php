<?php
/*
 **********************************************
 Copyright (c) 2006-2010 Anything-Digital.com
 **********************************************
 JCal Pro is a fork of the existing Extcalendar component for Joomla!
 (com_extcal_0_9_2_RC4.zip from mamboguru.com).
 Extcal (http://sourceforge.net/projects/extcal) was renamed
 and adapted to become a Mambo/Joomla! component by
 Matthew Friedman, and further modified by David McKinnis
 (mamboguru.com) to repair some security holes.

 This program is free software; you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation; either version 2 of the License, or
 (at your option) any later version.

 This header must not be removed. Additional contributions/changes
 may be added to this header as long as no information is deleted.
 **********************************************

 $Id: index.php 599 2010-03-19 17:35:30Z shumisha $

 **********************************************
 Get the latest version of JCal Pro at:
 http://dev.anything-digital.com//
 **********************************************
 */

/** ensure this file is being included by a parent file */
defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

// New language structure
$lang_info = array (
  'name' => 'Portuguese'
  ,'nativename' => 'Português' // Language name in native language. E.g: 'Français' for 'French'
  ,'locale' => array('pt-PT','Portuguese') // Standard locale alternatives for a specific language. For reference, go to: http://www.php.net/manual/en/function.setlocale.php
  ,'charset' => 'UTF-8' // For reference, go to : http://www.w3.org/International/O-charset-lang.html
  ,'direction' => 'ltr' // 'ltr' for Left to Right. 'rtl' for Right to Left languages such as Arabic.
  ,'author' => 'Allan Rodrigo Bomfim'
  ,'author_email' => 'allanrodrigo@gmx.net'
  ,'author_url' => 'www.infonet.com.br/allanrodrigo'
  ,'transdate' => '01/20/2005'
  );

  $lang_general = array (
  'yes' => 'Sim'
  ,'no' => 'Não'
  ,'back' => 'Voltar'
  ,'continue' => 'Continuar'
  ,'close' => 'Fechar'
  ,'errors' => 'Erros'
  ,'info' => 'Informação'
  ,'day' => 'Dia'
  ,'days' => 'Dias'
  ,'month' => 'Mês'
  ,'months' => 'Meses'
  ,'year' => 'Ano'
  ,'years' => 'Anos'
  ,'hour' => 'Hora'
  ,'hours' => 'Horas'
  ,'minute' => 'Minuto'
  ,'minutes' => 'Minutos'
  ,'everyday' => 'Todo Dia'
  ,'everymonth' => 'Todo Mês'
  ,'everyyear' => 'Todo Ano'
  ,'active' => 'Activo'
  ,'not_active' => 'Não Activo'
  ,'today' => 'Hoje'
  ,'signature' => 'Powered by %s'
  ,'expand' => 'Expandir'
  ,'collapse' => 'Encolher'
  ,'any_calendar' => 'Show all calendars'
  ,'noon' => 'noon'
  ,'midnight' => 'midnight'
  ,'am' => 'am'
  ,'pm' => 'pm'
  );

  // Date formats, For reference, go to : http://www.php.net/manual/en/function.strftime.php
  $lang_date_format = array (
  'full_date' => '%A, %d de %B de %Y' // e.g. Wednesday, June 05, 2002
  ,'full_date_time_24hour' => '%A, %d de %B de %Y às %H:%M' // e.g. Wednesday, June 05, 2002 At 21:05
  ,'full_date_time_12hour' => '%A, %d de %B de %Y às %I:%M %p' // e.g. Wednesday, June 05, 2002 At 9:05 pm
  ,'day_month_year' => '%d-%b-%Y'
  ,'local_date' => '%c'
  ,'mini_date' => '%a. %d %b, %Y'
  ,'month_year' => '%B %Y'
  ,'day_of_week' => array('Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado')
  ,'months' => array('Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro')
  // Jcal Pro 2.1.x
  ,'date_entry' => '%Y-%m-%d'
  );

  $lang_system = array (
  'system_caption' => 'Mensagem de Sistema'
  ,'page_access_denied' => 'Você não tem o nível de privilégio para acessar a esta página.'
  ,'page_requires_login' => 'Você deve estar registado para acessar a esta página.'
  ,'operation_denied' => 'Voê não tem o nível de privilégio para executar esta operação.'
  ,'section_disabled' => 'Esta secção está actualmente desabilitada!'
  ,'non_exist_cat' => 'A categoria seleccionada não existe!'
  ,'non_exist_event' => 'O evento seleccionado não existe!'
  ,'param_missing' => 'O parâmetro fornecido está incorrecto.'
  ,'no_events' => 'Não há eventos a mostrar'
  ,'config_string' => 'You are currently using \'%s\' running on %s, %s and %s.'
  ,'no_table' => 'A \'%s\' tabela não existe !'
  ,'no_anonymous_group' => 'A %s tabela não contém o grupo \'Anonymous\'  !'
  ,'calendar_locked' => 'Este serviço está em manutenção temporariamente. '
  ,'new_upgrade' => 'O sistema detectou uma nova versão. É recomendada a actualização agora. Clique "Continuar" para iniciar a actualização.'
  ,'no_profile' => 'Um erro ocorreu enquanto recuperamos a informação do seu perfil'
  // Mail messages
  ,'new_event_subject' => 'Novo Evento em %s'
  ,'event_notification_failed' => 'Um erro ocorreu durante o envio da notificação de activação por email!'
  
  ,'show_required_privileges' => 'Your user level is %s, while this requires to be %s'  // JCal 2.1
  ,'template_block_not_found' => '<b>Template error<b><br />Failed to find block \'%s\' in :<br /><pre>%s</pre>'
  ,'template_file_not_found' => '<b>JCAL Pro critical error</b>:<br />Unable to load template file %s!</b>'
  );

  // Message body for new event email notification
  $lang_system['event_notification_body'] = <<<EOT
O seguinte evento tem sido postado no {CALENDAR_NAME}

Título: "{TITLE}"
Data: "{DATE}"
Duração: "{DURATION}"

Você pode acessar a este evento clicando no link abaixo
ou copiando e colando no seu navegador.

{LINK}

Atenciosamente,

O administrador de {CALENDAR_NAME}

EOT;

  // Admin menu entries
  $lang_admin_menu = array (
  'login' => 'Login'
  ,'register' => 'Registar'
  ,'logout' => 'Logout <span style="color:#FF9922">[<span style="color:#606F79">%s</span>]</span>'
  ,'user_profile' => 'Meu Perfil'
  ,'admin_events' => 'Eventos'
  ,'admin_categories' => 'Categorias'
  ,'admin_groups' => 'Grupos'
  ,'admin_users' => 'Utilizadores'
  ,'admin_settings' => 'Configurações'
  );

  // Main menu entries
  $lang_main_menu = array (
  'add_event' => 'Adiccionar Evento'
  ,'cal_view' => 'Ver o Mês'
  ,'flat_view' => 'Listagem'
  ,'weekly_view' => 'Ver a Semana'
  ,'daily_view' => 'Hoje'
  ,'yearly_view' => 'Ver o Ano'
  ,'categories_view' => 'Categorias'
  ,'search_view' => 'Procurar'
  ,'ical_view' => 'get as iCal'
  ,'print_view' => 'Print'
  );

  // ======================================================
  // Add Event view
  // ======================================================

  $lang_add_event_view = array(
  'section_title' => 'Adicionar Evento'
  ,'edit_event' => 'Editar Evento [id%d] \'%s\''
  ,'update_event_button' => 'Actualizar Evento'

  // Event details
  ,'event_details_label' => 'Detalhes do Evento'
  ,'event_title' => 'Título do Evento'
  ,'event_desc' => 'Descrição do Evento'
  ,'event_cat' => 'Categoria'
  ,'choose_cat' => 'Seleccione a Categoria'
  ,'event_date' => 'Data do Evento'
  ,'day_label' => 'Dia'
  ,'month_label' => 'Mês'
  ,'year_label' => 'Ano'
  ,'start_date_label' => 'Início do Evento'
  ,'start_time_label' => 'em'
  ,'end_date_label' => 'Duração'
  ,'all_day_label' => 'Todo dia'
  // Contact details
  ,'contact_details_label' => 'Contacto - Detalhes'
  ,'contact_info' => 'Contacto - Info'
  ,'contact_email' => 'Contacto - Email'
  ,'contact_url' => 'Contacto - URL'
  // Repeat events
  ,'repeat_event_label' => 'Repetir Evento'
  ,'repeat_method_label' => 'Repetir Método'
  ,'repeat_none' => 'Não repetir este evento'
  ,'repeat_every' => 'Repetir todos'
  ,'repeat_days' => 'Dia(s)'
  ,'repeat_weeks' => 'Semana(s)'
  ,'repeat_months' => 'Mês(es)'
  ,'repeat_years' => 'Ano(s)'
  ,'repeat_end_date_label' => 'Repetir data final'
  ,'repeat_end_date_none' => 'Sem data final'
  ,'repeat_end_date_count' => 'Finalizado depois de %s ocorrência(s)'
  ,'repeat_end_date_until' => 'Repetir até'
  // new JCalpro 2
  ,'repeat_event_detached' => 'This event was part of a repetition series, but has been modified and separated from it'
  ,'repeat_event_detached_short' => 'Detached from recurrence'
  ,'repeat_event_not_detached' => 'This event is part of a repetition series'
  ,'repeat_edit_parent_event' => 'Edit parent event'
  ,'deleted_child_events' => 'Deleted %d previous instances'
  ,'created_child_events' => 'Created a total of %d repetitions of event %s. View this event by <a href="%s" >clicking here</a>.'  // Jcal Pro 2.1.x
  // Other details
  ,'other_details_label' => 'Outros Detalhes'
  ,'picture_file' => 'Arquivo de Imagem'
  ,'file_upload_info' => '(%d KBytes limite - válida extensões : %s )'
  ,'del_picture' => 'Apagar imagem atual ?'
  // Administrative options
  ,'admin_options_label' => 'Opção Administrativas'
  ,'auto_appr_event' => 'Evento Aprovado'

  // Error messages
  ,'no_title' => 'Você deve dar um título para o evento !'
  ,'no_desc' => 'Você deve dar uma descrição para este evento !'
  ,'no_cat' => 'Você deve selecionar uma categoria !'
  ,'date_invalid' => 'Você deve dar uma data válida para este evento !'
  ,'end_days_invalid' => 'O valor colocado no \'Days\' campo não é válido !'
  ,'end_hours_invalid' => 'O valor colocado no campo \'Hours\' não é válido !'
  ,'end_minutes_invalid' => 'O valor colocado no campo \'Minutes\' não é válido !'
  
  ,'non_valid_extension' => 'O formato de arquivo da imagem não é suportado ! (Válidas extensões: %s)'

  ,'file_too_large' => 'O arquivo é muito grande ! (%d KBytes limit)'
  ,'move_image_failed' => 'O sistema falhou ao mover a imagem !'
  ,'non_valid_dimensions' => 'A largura e altura da imagem é maior que %s pixels !'

  ,'recur_val_1_invalid' => 'O valor colocado como \'repeat interval\' não é válido. Este valor deve ser um número maior que \'0\' !'
  ,'recur_end_count_invalid' => 'O valor colocado como \'number of occurrences\' não é válido. Este valor deve ser um número maior que \'0\' !'
  ,'recur_end_until_invalid' => 'O \'repeat until\' date deve ser uma data posterior ao início do evento !'
  ,'no_recur_end_date' => 'A recurring event should have an end-date or a number of occurences'
  // new JCalpro 2
  ,'failed_existing_event_update' => 'Database error during update of event %s (%d)'
  ,'failed_child_event_deletion' => 'Database error deleting children of event %s (%d)'
  ,'failed_child_event_creation' => 'Database error creating children of event %s (%d)'
  ,'no_calendar' => 'You must select a calendar from the drop down menu !'
  ,'event_cal' => 'Calendar'
  ,'private_event' => 'Private event'
  ,'private_event_read_only' => 'Private event, others can read'
  ,'public_event' => 'Public event'
  ,'privacy' => 'Privacy'
  ,'failed_event_creation' => 'Database error while trying to create this event'
  // Misc. messages JCal 2.1
  ,'submit_event_pending' => 'Seu evento depende de aprovação. Obrigado pela sua participação !'
  ,'submit_event_approved' => 'Seu evento está automaticamente aprovado. View this event by <a href="%s" >clicking here</a>. Obrigado pela sua participação !'
  ,'event_repeat_msg' => 'Este evento está configurado para repetir'
  ,'event_no_repeat_msg' => 'Este evento não será repetido'
  ,'recur_start_date_invalid' => 'Start date is not valid. For a recurring event, start date must be on the first recurrence of the series (ie: if recurring every tuesday, start date has to be a tuesday)'
  
  // new JCalPro 2.1
  ,'repeat_daily' => 'Repeat daily'
  ,'repeat_weekly' => 'Repeat weekly'
  ,'repeat_monthly' => 'Repeat monthly'
  ,'repeat_yearly' => 'Repeat yearly'
  ,'rec_weekly_on' => 'on :'
  ,'rec_monthly_on' => 'on the:'
  ,'rec_yearly_on' => 'on the:'
  ,'rec_day_first' => 'first'
  ,'rec_day_second' => 'second'
  ,'rec_day_third' => 'third'
  ,'rec_day_fourth' => 'fourth'
  ,'rec_day_last' => 'last'
  ,'rec_day_day' => 'day'
  ,'rec_day_week_day' => 'week day'
  ,'rec_day_weekend_day' => 'week-end day'
  ,'rec_yearly_on_month_label' => 'in'
  );

  // ======================================================
  // daily view
  // ======================================================

  $lang_daily_event_view = array(
  'section_title' => 'Visualizar Dias'
  ,'next_day' => 'Dia Seguinte'
  ,'previous_day' => 'Dia Anterior'
  ,'no_events' => 'Não há eventos neste dia.'
  );

  // ======================================================
  // weekly view
  // ======================================================

  $lang_weekly_event_view = array(
  'section_title' => 'Visualizar Semana'
  ,'week_period' => '%s - %s'
  ,'next_week' => 'Próxima Semana'
  ,'previous_week' => 'Semana Anterior'
  ,'selected_week' => 'Semana %d'
  ,'no_events' => 'Não há eventos nesta semana'
  );

  // ======================================================
  // monthly view
  // ======================================================

  $lang_monthly_event_view = array(
  'section_title' => 'Visualizar Mês'
  ,'next_month' => 'Próximo Mês'
  ,'previous_month' => 'Mês Anterior'
  );

  // ======================================================
  // flat view
  // ======================================================

  $lang_flat_event_view = array(
  'section_title' => 'Listagem'
  ,'week_period' => '%s - %s'
  ,'next_month' => 'Próximo Mês'
  ,'previous_month' => 'Mês Anterior'
  ,'contact_info' => 'Contacto - Info'
  ,'contact_email' => 'Contacto - Email'
  ,'contact_url' => 'Contacto - URL'
  ,'no_events' => 'Não há eventos neste mês'
  );

  // ======================================================
  // Event view
  // ======================================================

  $lang_event_view = array(
  'section_title' => 'Evento: \'%s\''
  ,'display_event' => 'Evento: \'%s\''
  ,'cat_name' => 'Categoria'
  ,'event_start_date' => 'Data'
  ,'event_end_date' => 'Até'
  ,'event_duration' => 'Duração'
  ,'contact_info' => 'Contacto - Info'
  ,'contact_email' => 'Email'
  ,'contact_url' => 'URL'
  ,'no_event' => 'Não há eventos a serem exibidas.'
  ,'stats_string' => '<strong>%d</strong> Eventos no Total'
  ,'edit_event' => 'Editar Evento'
  ,'delete_event' => 'Apagar Evento'
  ,'delete_confirm' => 'Você tem certeza que quer apagar este evento?'
  
  );

  // ======================================================
  // Categories view
  // ======================================================

  $lang_cats_view = array(
  'section_title' => 'Mostrar Categorias'
  ,'cat_name' => 'Nome da Categoria'
  ,'total_events' => 'Total de Eventos'
  ,'upcoming_events' => 'Eventos Próximos'
  ,'no_cats' => 'Não há categorias a serem exibidas.'
  ,'stats_string' => 'Há <strong>%d</strong> Eventos em <strong>%d</strong> Categorias'
  );

  // ======================================================
  // Category Events view
  // ======================================================

  $lang_cat_events_view = array(
  'section_title' => 'Eventos sobre \'%s\''
  ,'event_name' => 'Nome do Evento'
  ,'event_date' => 'Data'
  ,'no_events' => 'Não há eventos nesta categoria.'
  ,'stats_string' => '<strong>%d</strong> Eventos como Total'
  );

  // ======================================================
  // cal_search.php
  // ======================================================

  $lang_event_search_data = array(
  'section_title' => 'Procurar no Calendário',
  'search_results' => 'Resultado da Procura',
  'category_label' => 'Categoria',
  'date_label' => 'Data',
  'no_events' => 'Não há eventos nesta categoria.',
  'search_caption' => 'Digite alguma palavra-chave...',
  'search_again' => 'Procurar Novamente',
  'search_button' => 'Procurar',
  // Misc.
  'no_results' => 'Nenhum resultado encontrado',  
  // Stats
  'stats_string1' => '<strong>%d</strong> evento(s) encontrados',
  'stats_string2' => '<strong>%d</strong> Evento(s) em <strong>%d</strong> página(s)'
  );

  // ======================================================
  // profile.php
  // ======================================================

  if (defined('PROFILE_PHP'))

  $lang_user_profile_data = array(
  'section_title' => 'Meu Perfil',
  'edit_profile' => 'Editar Meu Perfil',
  'update_profile' => 'Atualizar Meu Perfil',
  'actions_label' => 'Acções',
  // Account Info
  'account_info_label' => 'Informação da Conta',
  'user_name' => 'Login',
  'user_pass' => 'Senha',
  'user_pass_confirm' => 'Confirmar Senha',
  'user_email' => 'E-mail',
  'group_label' => 'Grupo',
  // Other Details
  'other_details_label' => 'Outras Informações',
  'first_name' => 'Nome',
  'last_name' => 'Sobrenome',
  'full_name' => 'Nome Completo',
  'user_website' => 'Home page',
  'user_location' => 'Localização',
  'user_occupation' => 'Ocupação',
  // Misc.
  'select_language' => 'Selecionar Idioma',
  'edit_profile_success' => 'Perfil actualizado com sucesso',
  'update_pass_info' => 'Deixe o campo de senha vazio caso não queira mudá-la',
  // Error messages
  'invalid_password' => 'Por favor, digite uma senha com apenas letras e números e entre 4 e 16 caracteres!',
  'password_is_username' => 'A senha deve ser diferente do login!',
  'password_not_match' =>'Senha não confirmada',
  'invalid_email' => 'Você deve fornecer um email válido!',
  'email_exists' => 'Outro utilizador já está registado com seu email. Por favor, digite um email diferente!',
  'no_email' => 'Você deve fornecer um email !',
  'invalid_email' => 'Você deve fornecer um email válido!',
  'no_password' => 'Você deve fornecer uma senha para esta nova conta!'
  );

  // ======================================================
  // register.php
  // ======================================================

  if (defined('USER_REGISTRATION_PHP'))

  $lang_user_registration_data = array(
  'section_title' => 'Utilizador Registado',
  // Step 1: Terms & Conditions
  'terms_caption' => 'Termos and Condições',
  'terms_intro' => 'Para prosseguir, você deve concordar com os seguintes termos:',
  'terms_message' => 'Por favor, reserve um momento para revisar esses termos abaixo. Se concordar com eles e desejar prosseguir com a instalação, simplesmente clique em "Eu concordo". Para cancelar este registro, simplesmente clique no botão \'back\'  do seu navegador.<br /><br />Lembre-se que nós não nos responsabilizamos por nenhum evento postado por utilizadores desta aplicação de calendário. Nós não garantimos a extadidão, integridade ou utilidade de nenhum evento postado, e não nos responsabilizamos pelo conteúdo de nenhum evento.<br /><br />As mensagens expressam as opiniões o autor do evento, não necessariamente as opiniões dos desenvolvedores desta aplicação de calendário. Todo utilizador que sentir que um evento afixado é desagradável, incetivamos a entrar em contacto conosco imediatamente por email. Nós podemos remover o conteúdo desagradável e nós faremos esforço para fazê-lo dentro de um espaço de tempo razoável, se nós determinarmos que a remoção é necessária.<br /><br />Você concorda, através do seu uso deste serviço, que você não usará este calendário para postar nenhum material que seja conscientemente falso ou defamatório, inexato, abusivo, odioso, vulgar, incômodo, obsceno, ameçador, sexualmente orientado, invasor de privacidade ou qualquer outra forma de violação da lei.w.<br /><br />Você concorda em não colocar material protegido por direitos autorais a não ser que os direitos autorais sejam seus ou a %s.',
  'terms_button' => 'Eu concordo',

  // Account Info
  'account_info_label' => 'Informação da Conta',
  'user_name' => 'Login',
  'user_pass' => 'Senha',
  'user_pass_confirm' => 'Confirmar Senha',
  'user_email' => 'E-mail',
  // Other Details
  'other_details_label' => 'Outras Informações',
  'first_name' => 'Nome',
  'last_name' => 'Sobrenome',
  'user_website' => 'Home page',
  'user_location' => 'Localização',
  'user_occupation' => 'Ocupação',
  'register_button' => 'Registar',

  // Stats
  'stats_string1' => '<strong>%d</strong> utilizadores',
  'stats_string2' => '<strong>%d</strong> utilizadores em <strong>%d</strong> página(s)',
  // Misc.
  'reg_nomail_success' => 'Obrigado pelo seu registro.',
  'reg_mail_success' => 'Um email com informação de como ativar sua conta foi enviado para você.',
  'reg_activation_success' => 'Parabéns! A sua conta está activada e você já se pode ligar. Obrigado pelo seu registo.',
  // Mail messages
  'reg_confirm_subject' => 'Registado em %s',

  // Error messages
  'no_username' => 'Você deve fornecer um nome para o login !',
  'invalid_username' => 'Por favor, digite um nome para o login que tenha somente letras e números  e entre 4 a 30 caracteres !',
  'username_exists' => 'O login que você digitou já existe. Por favor, tente registrar um outro nome para login !',
  'no_password' => 'Você deve fornecer uma senha !',
  'invalid_password' => 'Por favor, digite uma senha com apenas letras e números e entre 4 e 16 caracteres !',
  'password_is_username' => 'A senha deve ser diferentes do login !',
  'password_not_match' =>'Senha não confirmada',
  'no_email' => 'Você deve fornecer um email !',
  'invalid_email' => 'Você deve fornecer uma email válido  !',
  'email_exists' => 'Outro utilizador já está registado com seu email. Por favor, digite um email diferente !',
  'delete_user_failed' => 'Este utilizador não pode ser apagado',
  'no_users' => 'Não há contas de utilizador para exibir !',
  'already_logged' => 'Você já está logado como um membro !',
  'registration_not_allowed' => 'Utilizadors registados estão atualmente sem permissão !',
  'reg_email_failed' => 'Um erro ocorreu durante o envio da notificação de ativação por email !',
  'reg_activation_failed' => 'Um erro ocorreu durante o processo de ativação !'

  );
  // Message body for email activation
  $lang_user_registration_data['reg_confirm_body'] = <<<EOT
Obrigado pelo seu registro not {CALENDAR_NAME}

Seu login é : "{USERNAME}"
Sua senha é : "{PASSWORD}"

Para activar a sua conta, você precisa clicar no link abaixo ou copiar e colar no seu navegador.

{REG_LINK}

Atenciosamente,

O administrador do {CALENDAR_NAME}

EOT;

  // ======================================================
  // theme.php
  // ======================================================

  // To Be Done

  // ======================================================
  // functions.inc.php
  // ======================================================

  // To Be Done

  // ======================================================
  // dblib.php
  // ======================================================

  // To Be Done

  // ======================================================
  // admin_events.php
  // ======================================================

  if (defined('ADMIN_EVENTS_PHP'))

  $lang_event_admin_data = array(
  'section_title' => 'Administração de Evento',
  'events_to_approve' => 'Administração de Evento: Eventos para Aprovar',
  'upcoming_events' => 'Administração de Evento: Eventos Programados',
  'past_events' => 'Administração de Evento: Eventos Passados',
  'add_event' => 'Adicionar Novo Evento',
  'edit_event' => 'Editar Evento',
  'view_event' => 'Exibir Evento',
  'approve_event' => 'Aprovar Evento',
  'update_event' => 'Atualizar Informações do Evento',
  'delete_event' => 'Apagar Evento',
  'events_label' => 'Eventos',
  'auto_approve' => 'Auto-Aprovar',
  'date_label' => 'Data',
  'actions_label' => 'Acções',
  'events_filter_label' => 'Filtrar Eventos',
  'events_filter_options' => array('Mostrar todos eventos','Mostrar somente eventos não aprovados','Mostrar eventos programados','Mostrar somente eventos passados'),
  'picture_attached' => 'Imagem Anexada',
  // View Event
  'view_event_name' => 'Evento: \'%s\'',
  'event_start_date' => 'Data',
  'event_end_date' => 'Até',
  'event_duration' => 'Duração',
  'contact_info' => 'Contacto - Info',
  'contact_email' => 'Email',
  'contact_url' => 'URL',
  // General Info
  // Event form
  'edit_event_title' => 'Evento: \'%s\'',
  'cat_name' => 'Categoria',
  'event_start_date' => 'Data',
  'event_end_date' => 'Até',
  'contact_info' => 'Contacto - Info',
  'contact_email' => 'Email',
  'contact_url' => 'URL',
  'no_event' => 'Não há eventos a serem exibidos.',
  'stats_string' => '<strong>%d</strong> Eventos no Total',
  // Stats
  'stats_string1' => '<strong>%d</strong> evento(s)',
  'stats_string2' => 'Total: <strong>%d</strong> eventos na(s) %d página(s)',
  // Misc.
  'add_event_success' => 'Novo evento adicionado com sucesso',
  'edit_event_success' => 'Evento atualizado com sucesso',
  'approve_event_success' => 'Evento aprovado com sucesso',
  'delete_confirm' => 'Você tem certeza que quer apagar este evento ?',
  'delete_event_success' => 'Evento apagado com sucesso',
  'active_label' => 'Ativo',
  'not_active_label' => 'Inativo',
  // Error messages
  'no_event_name' => 'Você deve fornecer um nome para este evento !',
  'no_event_desc' => 'Você deve fornecer uma descrição para este evento !',
  'no_cat' => 'Você deve seleccionar a categoria para este evento !',
  'no_day' => 'Você deve seleccionar um dia !',
  'no_month' => 'Você deve seleccionar um mês !',
  'no_year' => 'Você deve seleccionar um ano !',
  'non_valid_date' => 'Por favor, entre com uma data válida !',
  'end_days_invalid' => 'Por favor, verifique se o campo \'Days\' sobre \'Duration\' possui apenas números !',
  'end_hours_invalid' => 'Por favor, verifique se o campo \'Hours\' sobre \'Duration\' possui apenas números !',
  'end_minutes_invalid' => 'Por favor, verifique se o campo \'Minutes\' sobre \'Duration\' possui apenas números !',
  'file_too_large' => 'A imagem é maior que %s KBytes !',
  'non_valid_extension' => 'O formato de arquivo da imagem não é suportado !',
  'delete_event_failed' => 'Este evento não pode ser apagado',
  'approve_event_failed' => 'Este evento não pode ser aprovado',
  'no_events' => 'Não há eventos para exibir !',
  'move_image_failed' => 'O sistema falhou ao mover a imagem !',
  'non_valid_dimensions' => 'A imagem tem largura ou altura maior que %s pixels !',

  'recur_val_1_invalid' => 'O valor digitado como \'repeat interval\' não é válido. Este valor deve ser um número maior que \'0\' !',
  'recur_end_count_invalid' => 'O valor digitado como \'number of occurrences\' não é válido. Este valor deve ser um número maior que \'0\' !',
  'recur_end_until_invalid' => 'A data do \'repeat until\' deve ser posterior a data inicial do evento !'

  );

  // ======================================================
  // admin_categories.php
  // ======================================================

  if (defined('ADMIN_CATS_PHP'))

  $lang_cat_admin_data = array(
  'section_title' => 'Categoria -  Administração',
  'add_cat' => 'Adicionar Nova Categoria',
  'edit_cat' => 'Editar Categoria',
  'update_cat' => 'Atualizar Informação da Categoria',
  'delete_cat' => 'Apagar Categoria',
  'events_label' => 'Eventos',
  'auto_approve' => 'Auto-Aprovar',
  'actions_label' => 'Ações',
  'users_label' => 'Utilizadores',
  'admins_label' => 'Admins',
  // General Info
  'general_info_label' => 'Informação Geral',
  'cat_name' => 'Nome da Categoria',
  'cat_desc' => 'Descrição da Categoria',
  'cat_color' => 'Cor',
  'pick_color' => 'Esclha uma Cor!',
  'status_label' => 'Status',
  'category_label' => 'Category permissions',
  // Administrative Options
  'admin_label' => 'Opções Administrativas',
  'auto_admin_appr' => 'Auto-aprovar posts dos administradores',
  'auto_user_appr' => 'Auto-aprovar posts dos utilizadores',
  // Stats
  'stats_string1' => '<strong>%d</strong> categorias',
  'stats_string2' => 'Ativo: <strong>%d</strong>&nbsp;&nbsp;&nbsp;Total: <strong>%d</strong>&nbsp;&nbsp;&nbsp;na(s) %d página(s)',
  // Misc.
  'add_cat_success' => 'Nova categoria adicionaca com sucesso',
  'edit_cat_success' => 'Categoria atualizada com sucesso',
  'delete_confirm' => 'Você está certo que quer apagar esta categoria ?',
  'delete_cat_success' => 'Categoria apagada com sucesso',
  'active_label' => 'Activo',
  'not_active_label' => 'Inactivo',
  // Error messages
  'no_cat_name' => 'Você deve fornecer um nome pare esta categoria !',
  'no_cat_desc' => 'Você deve fornecer uma descrição para esta categoria !',
  'no_color' => 'Voc~e deve fornecer uma cor para esta categoria !',
  'delete_cat_failed' => 'Esta categoria não pode ser apagada',
  'no_cats' => 'Não há categorias para exibir!',
  'cat_has_events' => 'Categoria %d contém %d evento(s) e não pode ser apagada!<br>Por favor, apague os eventos que restam nessa categoria e tente novamente!'
  ,'default' => 'Use default from settings'
  ,'no_cats_to_delete' => 'There is no category left to delete'
  );

  // JCAL pro 2
  // ======================================================
  // admin_calendars
  // ======================================================

  if (defined('ADMIN_CALS_PHP'))

  $lang_cal_admin_data = array(
  'section_title' => 'Calendars Administration',
  'add_cal' => 'Add New Calendar',
  'edit_cal' => 'Edit Calendar',
  'update_cal' => 'Update Calendar Info',
  'delete_cal' => 'Delete Calendar',
  'events_label' => 'Events',
  'visibility' => 'Visibility',
  'actions_label' => 'Actions',
  'users_label' => 'Users',
  'admins_label' => 'Admins',
  // General Info
  'general_info_label' => 'General Information',
  'cal_name' => 'Calendar Name',
  'cal_desc' => 'Calendar Description',
  'status_label' => 'Status',
  'calendar_label' => 'Calendar permissions',
  // Stats
  'stats_string1' => '<strong>%d</strong> calendars',
  'stats_string2' => 'Active: <strong>%d</strong>&nbsp;&nbsp;&nbsp;Total: <strong>%d</strong>&nbsp;&nbsp;&nbsp;on <strong>%d</strong> page(s)',
  // Misc.
  'add_cal_success' => 'New calendar added succesfully',
  'edit_cal_success' => 'Calendar updated succesfully',
  'delete_confirm' => 'Are you sure you want to delete this calendar ?',
  'delete_cal_success' => 'Calendar deleted succesfully',
  'active_label' => 'Active',
  'not_active_label' => 'Not Active',
  // Error messages
  'no_cal_name' => 'You must provide a name for this calendar !',
  'no_cal_desc' => 'You must provide a description for this calendar !',
  'delete_cal_failed' => 'This calendar cannot be deleted',
  'no_cals' => 'There are no calendars to display !',
  'cal_has_events' => 'Calendar #%d contains %d event(s) and therefore cannot be deleted! Please delete remaining events under this calendar and try again!',
  'default' => 'Use default from settings'
  ,'no_cals_to_delete' => 'There is no calendar left to delete'
  );

  // ======================================================
  // admin_users.php
  // ======================================================

  if (defined('ADMIN_USERS_PHP'))

  $lang_user_admin_data = array(
  'section_title' => 'Administrador',
  'add_user' => 'Adicionar Novo Utilizador',
  'edit_user' => 'Editar Informação de Utilizador',
  'update_user' => 'Atualizar Informação de Utilizador',
  'delete_user' => 'Apagar Conta de Utilizador',
  'last_access' => 'Último Acesso',
  'actions_label' => 'Acções',
  'active_label' => 'Activo',
  'not_active_label' => 'Não Activo',
  // Account Info
  'account_info_label' => 'Informação da Conta',
  'user_name' => 'Login',
  'user_pass' => 'Senha',
  'user_pass_confirm' => 'Confirmar Senha',
  'user_email' => 'E-mail',
  'group_label' => 'Grupo de Membros',
  'status_label' => 'Status da Conta',
  // Other Details
  'other_details_label' => 'Outras Informações',
  'first_name' => 'Nome',
  'last_name' => 'Sobrenome',
  'user_website' => 'Home page',
  'user_location' => 'Localização',
  'user_occupation' => 'Ocupação',
  // Stats
  'stats_string1' => '<strong>%d</strong> utilizadores',
  'stats_string2' => '<strong>%d</strong> utilizadores em %d página(s)',
  // Misc.
  'select_group' => 'Selecione uma...',
  'add_user_success' => 'Conta do utilizador adicionada com sucesso',
  'edit_user_success' => 'Conta do utilizador atualizada com sucesso',
  'delete_confirm' => 'Você está certo que deseja apagar esta conta?',
  'delete_user_success' => 'Conta de utilizador apagada com sucesso',
  'update_pass_info' => 'Deixa o compo de senha vazia se não deseja mudá-la',
  'access_never' => 'Nunca',
  // Error messages
  'no_username' => 'Você deve fornecer o login!',
  'invalid_username' => 'Por favor, digite um nome para o login que tenha somente letras e números  e entre 4 a 30 caracteres!',
  'invalid_password' => 'Por favor, digite uma senha com apenas letras e números e entre 4 e 16 caracteres !',
  'password_is_username' => 'A senha deve ser diferente do login !',
  'password_not_match' =>'Senha não confirmada',
  'invalid_email' => 'Você deve fornecer uma email válido!',
  'email_exists' => 'Outro utilizador já está registado com seu email. Por favor, digite um email diferente!',
  'username_exists' => 'O login que você digitou já existe. Por favor, tente registrar um outro nome para login!',
  'no_email' => 'Você deve fornecer um email !',
  'invalid_email' => 'Você deve fornecer uma email válido!',
  'no_password' => 'Você deve fornecer uma senha para esta conta!',
  'no_group' => 'Por favor selecione um grupo de mebros para este utilizador!',
  'delete_user_failed' => 'Esta conta de utilizador não pode ser apagada',
  'no_users' => 'Não há contas de utilizador para exibir!'

  );

  // ======================================================
  // admin_groups.php
  // ======================================================

  if (defined('ADMIN_GROUPS_PHP'))

  $lang_group_admin_data = array(
  'section_title' => 'Grupo - Administração',
  'add_group' => 'Adicionar Novo Grupo',
  'edit_group' => 'Editar Grupo',
  'update_group' => 'Actualizar Informações de Grupo',
  'delete_group' => 'Apagar Grupo',
  'view_group' => 'Visualizar Grupo',
  'users_label' => 'Membros',
  'actions_label' => 'Acções',
  // General Info
  'general_info_label' => 'Informação Geral',
  'group_name' => 'Nome do Grupo',
  'group_desc' => 'Descrição do Grupo',
  // Group Access Level
  'access_level_label' => 'Nível de Acesso do Grupo',
  'Administrator' => 'Utilizadores deste grupo tem acesso administrativo',
  'can_manage_accounts' => 'Utilizadores deste grupo podem manipular contas',
  'can_change_settings' => 'Utilizadores deste grupo podem mudar configurações do calendário',
  'can_manage_cats' => 'Utilizadores deste grupo podem manipular categorias',
  'upl_need_approval' => 'Postar eventos requer aprovação dos administradores',
  // Stats
  'stats_string1' => '<strong>%d</strong> grupos',
  'stats_string2' => 'Total: <strong>%d</strong> grupos em %d página(s)',
  'stats_string3' => 'Total: <strong>%d</strong> utilizadores em %d página(s)',
  // View Group Members
  'group_members_string' => 'Membros de \'%s\' grupos',
  'username_label' => 'Login',
  'firstname_label' => 'Primeiro Nome',
  'lastname_label' => 'Sobrenome',
  'email_label' => 'Email',
  'last_access_label' => 'Último Acesso',
  'edit_user' => 'Editar Utilizador',
  'delete_user' => 'Apagar Utilizador',
  // Misc.
  'add_group_success' => 'Novo grupo adicionado com sucesso',
  'edit_group_success' => 'Grupo actualizado com sucesso',
  'delete_confirm' => 'Está certo que deseja atualizar este grupo?',
  'delete_user_confirm' => 'Você está certo que deseja apagar este grupo?',
  'delete_group_success' => 'Grupo apagado com sucesso',
  'no_users_string' => 'Não há utilizador neste grupo',
  // Error messages
  'no_group_name' => 'Você deve fornecer um nome para o grupo!',
  'no_group_desc' => 'Você deve fornecer uma descrição para este grupo!',
  'delete_group_failed' => 'Este grupo não pode ser apagado',
  'no_groups' => 'Não há grupos para serem exibidos!',
  'group_has_users' => 'Este grupo contém %d utilizador(s) e não pode ser apagado!<br>Favor, desvincule os utilizadores restantes deste grupo e tente novamente!'

  );

  // ======================================================
  // admin_settings.php / admin_settings_template.php /
  // admin_settings_updates.php
  // ======================================================

  $lang_settings_data = array(
  'section_title' => 'Configurações do Calendário'
  // Links
  ,'admin_links_text' => 'Escolha a Seção'
  ,'admin_links' => array('Configurações Principais','Configuração de Template','Atualizar Programa')
  // General Settings
  ,'general_settings_label' => 'Configurações Gerais'
  ,'calendar_name' => 'Nome do Calendário'
  ,'calendar_description' => 'Descrição do Calendário'
  ,'calendar_admin_email' => 'Email do Administrador do Calendário'
  ,'cookie_name' => 'Nome para o cookie usado pelo script'
  ,'cookie_path' => 'Path para o cookie usado pelo script'
  ,'debug_mode' => 'Habilitar debug mode'
  ,'calendar_status' => 'Status do Calendário'
  // Environment Settings
  ,'env_settings_label' => 'Configurações Ambiente'
  ,'lang' => 'Idioma'
  ,'lang_name' => 'Idioma'
  ,'lang_native_name' => 'Nome Nativo'
  ,'lang_trans_date' => 'Traduzido em'
  ,'lang_author_name' => 'Autor'
  ,'lang_author_email' => 'E-mail'
  ,'lang_author_url' => 'Website'
  ,'charset' => 'Character encoding'
  ,'theme' => 'Tema'
  ,'theme_name' => 'Nome do Tema'
  ,'theme_date_made' => 'Feito em'
  ,'theme_author_name' => 'Autor'
  ,'theme_author_email' => 'E-mail'
  ,'theme_author_url' => 'Website'
  ,'timezone' => 'Fuso Horário para horário de verão (DST)'
  ,'time_format' => 'Formato de exibição de horas'
  ,'24hours' => '24 Hours'
  ,'12hours' => '12 Hours'
  ,'auto_daylight_saving' => 'Ajustar automaticamente horário de verão (DST)'
  ,'main_table_width' => 'Largura da tabela principal (Pixels ou %)'
  ,'day_start' => 'Semana começa em'
  ,'default_view' => 'Visualização padrão'
  ,'search_view' => 'Habilitar procura'
  ,'archive' => 'Mostrar últimos eventos'
  ,'events_per_page' => 'Número de eventos por página'
  ,'sort_order' => 'Ordem de classificação padrão'
  ,'sort_order_title_a' => 'Título ascendente'
  ,'sort_order_title_d' => 'Título descendente'
  ,'sort_order_date_a' => 'Date ascendente'
  ,'sort_order_date_d' => 'Date descendente'
  ,'show_recurrent_events' => 'Mostrar eventos recorrentes'
  ,'multi_day_events' => 'Eventos em vários dias'
  ,'multi_day_events_all' => 'Mostrar toda escala de data'
  ,'multi_day_events_bounds' => 'Mostrar data inicial e final somente'
  ,'multi_day_events_start' => 'Mostrar data inicial somente'
  // User Settings
  ,'user_settings_label' => 'Configurações do Utilizador'
  ,'allow_user_registration' => 'Permitir registro de utilizadores'
  ,'reg_duplicate_emails' => 'Permitir emails duplicados'
  ,'reg_email_verify' => 'Habilitar ativação de conta através de email'
  // Event View
  ,'event_view_label' => 'Visualizar Evento'
  ,'popup_event_mode' => 'Evento Pop-up'
  ,'popup_event_width' => 'Largura da Janela Pop-up'
  ,'popup_event_height' => 'Altura da Janela Pop-up'
  // Add Event View
  ,'add_event_view_label' => 'Adicionar Visulização de Evento'
  ,'add_event_view' => 'Ativado'
  ,'addevent_allow_html' => 'Permitir <b>BB Code</b> '
  ,'addevent_allow_contact' => 'Permitir Contacto'
  ,'addevent_allow_email' => 'Permitir Email'
  ,'addevent_allow_url' => 'Permitir URL'
  ,'addevent_allow_picture' => 'Permitir Imagens'
  ,'new_post_notification' => 'Postar Nova Notificação'
  // Calendar View
  ,'calendar_view_label' => 'Visualizar Calendário (Mensal) '
  ,'monthly_view' => 'Ativado'
  ,'cal_view_show_week' => 'Exibir Número da Semana'
  ,'cal_view_max_chars' => 'Maximo de Caracteres na Descrição'
  // Flyer View
  ,'flyer_view_label' => 'Visualizar Flyer'
  ,'flyer_view' => 'Ativado'
  ,'flyer_show_picture' => 'Exibir imagens na Flyer View'
  ,'flyer_view_max_chars' => 'Maximo de Caracteres na Descrição'
  // Weekly View
  ,'weekly_view_label' => 'Visualizar Semanas'
  ,'weekly_view' => 'Ativado'
  ,'weekly_view_max_chars' => 'Maximo de Caracteres na Descrição'
  // Daily View
  ,'daily_view_label' => 'Visualizar Dias'
  ,'daily_view' => 'Ativado'
  ,'daily_view_max_chars' => 'Maximo de Caracteres na Descrição'
  // Categories View
  ,'categories_view_label' => 'Visualizar Categorias'
  ,'cats_view' => 'Ativado'
  ,'cats_view_max_chars' => 'Maximo de Caracteres na Descrição'
  // Mini Calendar
  ,'mini_cal_label' => 'Mini Calendário'
  ,'mini_cal_def_picture' => 'Imagem Padrão'
  ,'mini_cal_display_picture' => 'Exibir Imagem'
  ,'mini_cal_diplay_options' => array('Nenhuma','Imagem Padrão', 'Imagem Diária','Imagem Semanal','Imagem Aleatória')
  // Mail Settings
  ,'mail_settings_label' => 'Configuração de Correio'
  ,'mail_method' => 'Método para Enviar Email'
  ,'mail_smtp_host' => 'SMTP Hosts (separada por ponto e vírgula ;)'
  ,'mail_smtp_auth' => ' SMTP Autenticação'
  ,'mail_smtp_username' => 'SMTP Login'
  ,'mail_smtp_password' => 'SMTP Senha'
  
  // Picture Settings
  ,'picture_settings_label' => 'Configurações de Imagems'
  ,'max_upl_dim' => 'Max. largura ou altura para enviar imagens'
  ,'max_upl_size' => 'Max. tamanho para enviar imagens (em Bytes)'
  ,'picture_chmod' => 'Modo padrão para imagens (CHMOD) (em Octal)'
  ,'allowed_file_extensions' => 'Extensões de arquivo permitidos para upload de imagens'
  // Form Buttons
  ,'update_config' => 'Salvar Nova Configuração'
  ,'restore_config' => 'Restaurar Padrões'
  // Misc.
  ,'update_settings_success' => 'Configuração salva com sucesso'
  ,'restore_default_confirm' => 'Você ter certeza que quer restaurar as configurações padrão?'
  // Template Configuration
  ,'template_type' => 'Tipo de Template'
  ,'template_header' => 'Personalizar Cabeçalho'
  ,'template_footer' => 'Personalizar Rodapé'
  ,'template_status_default' => 'Usar tema template padrão'
  ,'template_status_custom' => 'Usar o seguinte template:'
  ,'template_custom' => 'Personalizar template'

  ,'info_meta' => 'Meta Information'
  ,'info_status' => 'Controle de Status'
  ,'info_status_default' => 'Desabilitar este índice'
  ,'info_status_custom' => 'Exibir o seguinte índice:'
  ,'info_custom' => 'Personalizar Índice'

  ,'dynamic_tags' => 'Tags Dinâmicas'

  // Product Updates
  ,'updates_check_text' => 'Por favor espera enquanto nós recuperamos a informação do servidor...'
  ,'updates_no_response' => 'Nenhuma resposta do servidor. Por favor tente novamente mais tarde.'
  ,'avail_updates' => 'Atualização Disponível'
  ,'updates_download_zip' => 'Download ZIP package (.zip)'
  ,'updates_download_tgz' => 'Download TGZ package (.tar.gz)'
  ,'updates_released_label' => 'Release Date: %s'
  ,'updates_no_update' => 'Você está executando a última versão disponível. Não é necessário atualização.'
  );

  // ======================================================
  // cal_mini.inc.php
  // ======================================================

  $lang_mini_cal = array(
  'def_pic' => 'Imagem Padrão'
  ,'daily_pic' => 'Imagem do Dia (%s)'
  ,'weekly_pic' => 'Imagem da Semana (%s)'
  ,'rand_pic' => 'Imagem Aleatória (%s)'
  ,'post_event' => 'Postar Novo Evento'
  ,'num_events' => '%d evento(s)'
  ,'selected_week' => 'Semana %d'
  );

  // ======================================================
  // extcalendar.php
  // ======================================================

  // To Be Done

  // ======================================================
  // config.inc.php
  // ======================================================

  // To Be Done

  // ======================================================
  // install.php
  // ======================================================

  // To Be Done

  // ======================================================
  // login.php
  // ======================================================

  if (defined('LOGIN_PHP'))

  $lang_login_data = array(
  'section_title' => 'Tela de Login'
  // General Settings
  ,'login_intro' => 'Digitar seu login e senha para entrar'
  ,'username' => 'Login'
  ,'password' => 'Senha'
  ,'remember_me' => 'Lembre-me'
  ,'login_button' => 'Entrar'
  // Errors
  ,'invalid_login' => 'Por favor, verifique seu login e senha e tente novamente !'
  ,'no_username' => 'Você deve digitar o login !'
  ,'already_logged' => 'Você já está logado !'
  );

  // ======================================================
  // logout.php
  // ======================================================

  // pt-PT Language - UTF-8  - by horus_68 www.joomlapt.com

  // ======================================================
  // latest_events module
  // ======================================================

  $lang_latest_events = array(
  'view_full_cal' => 'Ver calendário'
  ,'add_new_event' => 'Adicionar evento'
  ,'recent_events' => 'Eventos recentes'
  ,'no_events_scheduled' => 'Não existem eventos programados.'
  ,'more_days' => ' Mais dias '
  ,'days_ago' => ' Dias atrás'
  );

  // New defined constants, used to make a start with new language system

  if (!defined('_EXTCAL_THEMES_INSTALL_HEADING'))
  {
    DEFINE('_EXTCAL_THEMES_INSTALL_HEADING', 'Gestor de temas JCal Pro');

    //Common
    DEFINE('_EXTCAL_VERSION', 'Versão');
    DEFINE('_EXTCAL_DATE', 'Data');
    DEFINE('_EXTCAL_AUTHOR', 'Autor');
    DEFINE('_EXTCAL_AUTHOR_EMAIL', 'E-mail do Autor');
    DEFINE('_EXTCAL_AUTHOR_URL', 'URL de Autor');
    DEFINE('_EXTCAL_PUBLISHED', 'Publicado');

    //Plugins
    DEFINE('_EXTCAL_THEME_PLUGIN', 'Tema');
    DEFINE('_EXTCAL_THEME_PLUGCOM', 'Tema/Comando');
    DEFINE('_EXTCAL_THEME_NAME', 'Nome');
    DEFINE('_EXTCAL_THEME_HEADING', 'Gestor de temas JCal Pro');
    DEFINE('_EXTCAL_THEME_FILTER', 'Filtro');
    DEFINE('_EXTCAL_THEME_ACCESS_LIST', 'Lista de Acesso');
    DEFINE('_EXTCAL_THEME_ACCESS_LVL', 'Nível de Acesso');
    DEFINE('_EXTCAL_THEME_CORE', 'Sistema');
    DEFINE('_EXTCAL_THEME_DEFAULT', 'Padrão');
    DEFINE('_EXTCAL_THEME_ORDER', 'Ordem');
    DEFINE('_EXTCAL_THEME_ROW', 'Linha');
    DEFINE('_EXTCAL_THEME_TYPE', 'Tipo');
    DEFINE('_EXTCAL_THEME_ICON', 'Ícone');
    DEFINE('_EXTCAL_THEME_LAYOUT_ICON', 'Ícone de Aspecto');
    DEFINE('_EXTCAL_THEME_DESC', 'Descrição');
    DEFINE('_EXTCAL_THEME_EDIT', 'Editar');
    DEFINE('_EXTCAL_THEME_NEW', 'Novo');
    DEFINE('_EXTCAL_THEME_DETAILS', 'Detalhes Plugin');
    DEFINE('_EXTCAL_THEME_PARAMS', 'Parâmetros');
    DEFINE('_EXTCAL_THEME_ELMS', 'Elementos');
    //Plugin Installer
    DEFINE('_EXTCAL_THEMES_INSTALL_MSG', 'Apenas são exibidos os temas que podem ser desinstalados - Os temas do sistema não podem ser removidos.');
    DEFINE('_EXTCAL_THEME_NONE', 'Não existem temas não-sistema instalados');

    //Language Manager
    DEFINE('_EXTCAL_LANG_HEADING', 'Gestor de idiomas EXTCAL');
    DEFINE('_EXTCAL_LANG_LANG', 'Idioma');

    //Language Installer
    DEFINE('_EXTCAL_LANG_HEADING_INSTALL', 'Instalar novo idioma EXTCAL');
    DEFINE('_EXTCAL_LANG_BACK', 'Voltar ao gestor de idiomas');
    //

    //Global Installer
    DEFINE('_EXTCAL_INS_PACKAGE_UPLOAD', 'Enviar ficheiros');
    DEFINE('_EXTCAL_INS_PACKAGE_FILE', 'Pacote de ficheiros');
    DEFINE('_EXTCAL_INS_INSTALL', 'Instalar de Directório');
    DEFINE('_EXTCAL_INS_INSTALL_DIR', 'Directório de Instalação');
    DEFINE('_EXTCAL_INS_UPLOAD_BUTTON', 'Enviar &amp; Instalar');
    DEFINE('_EXTCAL_INS_INSTALL_BUTTON', 'Instalar');
  }
  