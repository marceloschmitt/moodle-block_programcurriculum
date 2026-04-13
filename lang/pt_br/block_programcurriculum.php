<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Plugin file for block_programcurriculum.
 *
 * @package    block_programcurriculum
 * @copyright  2026 Marcelo Augusto Rauh Schmitt <marcelo.schmitt@poa.ifrs.edu.br>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'Currículo do programa';
$string['pageheading'] = 'Currículo do programa';
$string['studentprogress'] = 'Progresso do aluno';
$string['blockconfiginfo'] = 'Este bloco fornece mapeamento de currículo e visualização de progresso.';
$string['programcurriculum:addinstance'] = 'Adicionar bloco de currículo do programa';
$string['programcurriculum:myaddinstance'] = 'Adicionar bloco de currículo do programa ao Painel';
$string['programcurriculum:manage'] = 'Gerenciar currículos';
$string['programcurriculum:import'] = 'Importar CSV de currículo';
$string['programcurriculum:viewownprogress'] = 'Ver próprio progresso no currículo';
$string['programcurriculum:viewallprogress'] = 'Ver progresso de todos os alunos no currículo';
$string['managecurricula'] = 'Gerenciar currículos';
$string['importcsv'] = 'Importar CSV';
$string['viewprogress'] = 'Ver progresso';
$string['viewcurriculum'] = 'Ver currículo';
$string['curriculumview_noprogram'] = 'Este curso não está vinculado a nenhum programa.';
$string['nocapability'] = 'Você não tem permissão para visualizar este bloco.';
$string['curricula'] = 'Currículos';
$string['listofprograms'] = 'Lista de programas';
$string['addcurriculum'] = 'Adicionar programa';
$string['editcurriculum'] = 'Editar programa';
$string['curriculumname'] = 'Nome do programa';
$string['curriculumcode'] = 'Código do programa';
$string['duplicatecurriculumcode'] = 'Já existe um programa com este código. Por favor, escolha outro código.';
$string['duplicatecurriculumname'] = 'Já existe um programa com este nome. Por favor, escolha outro nome.';
$string['curriculumdescription'] = 'Descrição';
$string['numterms'] = 'Número de períodos';
$string['numterms_help'] = 'Quantos períodos este programa possui? Cada disciplina será atribuída a um período.';
$string['invalidnumterms'] = 'O número de períodos deve ser pelo menos 1.';
$string['term'] = 'Período';
$string['term_help'] = 'A qual período esta disciplina pertence?';
$string['courses'] = 'Disciplinas';
$string['nocourses'] = 'Nenhuma disciplina encontrada.';
$string['addcourse'] = 'Adicionar disciplina';
$string['editcourse'] = 'Editar disciplina';
$string['coursename'] = 'Nome da disciplina';
$string['coursecode'] = 'Código da disciplina';
$string['equivalencecode'] = 'Código de equivalência';
$string['equivalencecode_help'] = 'Código alternativo para esta disciplina. O mapeamento automático ' .
    'pode usar tanto o código original quanto o código de equivalência para encontrar cursos ' .
    'Moodle. Opcional.';
$string['duplicatecoursecode'] = 'Este código de disciplina já está em uso.';
$string['deletecoursebody'] = 'Isso excluirá "{$a}".';
$string['deletecoursetitle'] = 'Excluir disciplina?';
$string['deletecourseconfirm'] = 'Excluir esta disciplina?';
$string['coursedeleted'] = 'Disciplina excluída.';
$string['coursedeletemappings'] = 'Você não pode excluir uma disciplina que possui mapeamentos.';
$string['courseformerror'] = 'Por favor, corrija os erros no formulário da disciplina.';
$string['curriculumformerror'] = 'Por favor, corrija os erros no formulário do currículo.';
$string['duplicatecurriculumnameorcode'] = 'O nome ou código do programa já existe. Por favor, escolha valores diferentes.';
$string['sortorder'] = 'Ordem';
$string['moveto'] = 'Mover para posição';
$string['movemodaltitle'] = 'Mover disciplina';
$string['movemodaltitlewithname'] = 'Mover disciplina: {$a}';
$string['movepositionhelp'] = 'Informe uma posição de 1 a {$a}.';
$string['movepositionclick'] = 'Clique em uma linha pontilhada para mover para essa posição.';
$string['movepositioninvalid'] = 'A posição deve estar entre 1 e o último item.';
$string['listofcourses'] = 'Lista de disciplinas';
$string['backtoprograms'] = 'Voltar aos programas';
$string['mappings'] = 'Mapeamentos';
$string['addmapping'] = 'Adicionar mapeamento';
$string['manualmapping'] = 'Mapeamento manual';
$string['automaticmapping'] = 'Mapeamento automático';
$string['automaticmappingdone'] = 'Mapeamento automático concluído. {$a} mapeamento(s) criado(s).';
$string['automaticmappingnocode'] = 'A disciplina externa não possui código. Não é possível executar o mapeamento automático.';
$string['automaticmappingdonecurriculum'] = 'Mapeamento automático concluído. {$a} mapeamento(s) criado(s) para as disciplinas do currículo.';
$string['assistedmapping'] = 'Mapeamento assistido';
$string['assistedmapping_help'] = 'Para cada disciplina externa, são listados cursos do Moodle cujo ' .
    'nome contém o nome ou código da disciplina. Selecione os mapeamentos que deseja criar e ' .
    'clique em Confirmar. Cursos já mapeados não aparecem.';
$string['assistedmapping_select'] = 'Selecione os cursos Moodle para mapear:';
$string['assistedmapping_nosuggestions'] = 'Nenhum curso Moodle encontrado contendo este nome ou código, ou todas as correspondências já estão mapeadas.';
$string['assistedmapping_confirm'] = 'Confirmar mapeamentos selecionados';
$string['assistedmappingdone'] = '{$a} mapeamento(s) criado(s).';
$string['editmapping'] = 'Editar requisito';
$string['move'] = 'Mover';
$string['deletemappingconfirm'] = 'Excluir este mapeamento?';
$string['deletemappingbody'] = 'Isso excluirá "{$a}".';
$string['deletemappingtitle'] = 'Excluir mapeamento?';
$string['mappingdeleted'] = 'Mapeamento excluído.';
$string['mappedmoodlecourse'] = 'Curso Moodle mapeado';
$string['mappingofcourse'] = 'Mapeamento da disciplina externa {$a}';
$string['listofmappings'] = 'Lista de mapeamentos';
$string['listofmappingsforcourse'] = 'Lista de mapeamentos - {$a}';
$string['backtocourses'] = 'Voltar às disciplinas';
$string['mappingformerror'] = 'Por favor, corrija os erros no formulário de mapeamento.';
$string['duplicatemapping'] = 'Este curso Moodle já está mapeado para esta disciplina externa.';
$string['courseplaceholder'] = 'Digite para buscar uma disciplina';
$string['importtitle'] = 'Importar CSV de currículo';
$string['importhelp'] = 'Colunas do CSV: curriculum_code, curriculum_name, course_code, course_name, ' .
    'moodle_course_id, sortorder, curriculum_description. Opcionais: numterms, term, ' .
    'equivalence_code';
$string['importsuccess'] = 'Importação CSV concluída.';
$string['importerrors'] = 'Erros de importação';
$string['viewtitle'] = 'Progresso do currículo';
$string['progressview'] = 'Visualização de progresso';
$string['progresspercent'] = 'Progresso';
$string['progress_intro'] = 'Esta página mostra seu progresso no {$a}. O primeiro indicador depende ' .
    'das disciplinas que você marcar como concluídas; o segundo é automático (disciplinas que você ' .
    'já cursou). As cores na lista indicam: inscrito em curso ativo, cursou em período anterior ou ' .
    'concluído. Lembre-se de marcar as disciplinas que já concluiu para acompanhar seu progresso.';
$string['progress_optional_notincluded'] = 'Disciplinas opcionais e atividades complementares não são consideradas no cálculo do progresso.';
$string['progresslegend'] = 'Legenda:';
$string['progresslegend_completed'] = 'Concluída';
$string['progresslegend_active'] = 'Inscrito em curso ativo';
$string['progresslegend_ended'] = 'Inscrito em curso encerrado';
$string['student'] = 'Aluno';
$string['completionstatus'] = 'Status de conclusão';
$string['completed'] = 'Concluído';
$string['notcompleted'] = 'Não concluído';
$string['choosecurriculum'] = 'Escolher currículo';
$string['listofstudents'] = 'Lista de alunos';
$string['clickstudentprogress'] = 'Clique em qualquer aluno para ver o progresso.';
$string['backtostudents'] = 'Voltar à lista de alunos';
$string['viewstudentprogress'] = 'Ver progresso';
$string['nostudents'] = 'Nenhum aluno encontrado neste curso.';
$string['nocurricula'] = 'Nenhum currículo encontrado.';
$string['deletecurriculumconfirm'] = 'Excluir este programa?';
$string['deletecurriculumtitle'] = 'Excluir programa?';
$string['deletecurriculumbody'] = 'Isso excluirá "{$a}".';
$string['curriculumdeleted'] = 'Programa excluído.';
$string['curriculumdeletecourses'] = 'Não é possível excluir um programa que possui disciplinas. Remova todas as disciplinas primeiro.';
$string['deleteallcourses'] = 'Excluir todas as disciplinas';
$string['deleteallcoursestitle'] = 'Excluir todas as disciplinas do programa?';
$string['deleteallcoursesconfirm'] = 'Isso excluirá todas as disciplinas de "{$a}". Os mapeamentos também serão removidos. Esta ação não pode ser desfeita.';
$string['deleteallcoursesdone'] = 'Todas as disciplinas do programa foram excluídas.';
$string['nomappings'] = 'Nenhum mapeamento encontrado.';
$string['csvfile'] = 'Arquivo CSV';
$string['upload'] = 'Enviar';
$string['importtext_content'] = 'Conteúdo do arquivo';
$string['importtext_format'] = 'Formato do arquivo de importação';
$string['importtext_format_help'] = 'Uma linha com o nome do programa; em seguida, para cada período: ' .
    'uma linha com o semestre (ex.: 1 ou 1º Semestre) e as linhas seguintes com o nome de cada ' .
    'disciplina do período. Repita o semestre e as disciplinas para os próximos períodos.';
$string['importtext_format_short'] = 'Formato: primeira linha = nome do programa; depois, para cada ' .
    'período: uma linha com o semestre (ex.: 1 ou 1º Semestre) e as próximas linhas com as ' .
    'disciplinas do período.';
$string['importtext_file'] = 'Ou envie um arquivo';
$string['importtext_file_help'] = 'Envie um arquivo .txt (ou .csv) no mesmo formato: nome do programa, depois semestre e disciplinas por período.';
$string['importtext_preview'] = 'Visualizar';
$string['importtext_preview_title'] = 'Pré-visualização da importação';
$string['importtext_terms_count'] = 'Períodos';
$string['importtext_preview_nosave'] = 'Nenhum dado foi gravado. Esta é apenas a pré-visualização.';
$string['importtext_empty'] = 'O arquivo enviado está vazio.';
$string['importtext_nofile'] = 'Envie um arquivo .txt ou .csv.';
$string['importtext_noprogramname'] = 'A primeira linha (nome do programa) está vazia.';
$string['importtext_semester_first'] = 'Linha {$a}: espere uma linha de semestre antes das disciplinas.';
$string['importtext_nosemesters'] = 'Nenhum período encontrado. Use linhas com número (ex.: 1, 2) ou com a palavra "semestre".';
$string['importtext_noprograms'] = 'Nenhum programa encontrado. A primeira linha deve ser o nome do programa.';
$string['importtext_semester_before_program'] = 'Linha {$a}: espere o nome do programa antes de um semestre (ou separe programas com linhas em branco).';
$string['import_do'] = 'Importar para o BD';
$string['import_success'] = 'Importação concluída. {$a} programa(s) importado(s).';
$string['import_curriculum_exists'] = 'Programa já existe (código: {$a}). Importação desse programa ignorada.';
$string['import_course_exists'] = 'Disciplina já existe (código: {$a}). Importação do programa ignorada.';
$string['courseidrequired'] = 'O ID do curso é obrigatório para visualizar o progresso.';
$string['mappedcourse'] = 'Disciplina mapeada';
$string['externalcourse'] = 'Disciplina externa';
$string['program'] = 'Programa';
$string['thisprogram'] = 'deste programa';
$string['enrolled'] = 'Inscrito';
$string['enrolleddisciplinescount'] = '{$a} disciplina(s)';
$string['currentdisciplinescount'] = 'Número de disciplinas correntes:';
$string['currentsubscriptions'] = 'Inscrições correntes';
$string['currentenrollment'] = 'Inscrição corrente';
$string['progressbyenrollment'] = 'por inscrição';
$string['progressbycompletion'] = 'por conclusão';
$string['progressbycompletion_header'] = 'Progresso por conclusão';
$string['progressbyenrollment_header'] = 'Progresso por inscrição';
$string['notenrolled'] = 'Não inscrito';
$string['moodlecoursesforexternal'] = 'Cursos Moodle para {$a}';
$string['moodlecourse_deleted'] = 'Curso Moodle excluído (ID: {$a})';
$string['markascompleted'] = 'Marcar como concluída';
$string['markedcompleted'] = 'Concluída';
$string['markdisciplinescompleted'] = 'Marque as disciplinas que você concluiu';
$string['close'] = 'Fechar';
