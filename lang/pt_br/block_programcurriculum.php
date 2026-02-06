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
$string['importhelp'] = 'Colunas do CSV: curriculum_code, curriculum_name, course_code, course_name, moodle_course_id, sortorder, curriculum_description. Opcionais: numterms, term';
$string['importsuccess'] = 'Importação CSV concluída.';
$string['importerrors'] = 'Erros de importação';
$string['viewtitle'] = 'Progresso do currículo';
$string['progressview'] = 'Visualização de progresso';
$string['progresspercent'] = 'Progresso';
$string['student'] = 'Aluno';
$string['completionstatus'] = 'Status de conclusão';
$string['completed'] = 'Concluído';
$string['notcompleted'] = 'Não concluído';
$string['choosecurriculum'] = 'Escolher currículo';
$string['listofstudents'] = 'Lista de alunos';
$string['backtostudents'] = 'Voltar à lista de alunos';
$string['viewstudentprogress'] = 'Ver progresso';
$string['nostudents'] = 'Nenhum aluno encontrado neste curso.';
$string['nocurricula'] = 'Nenhum currículo encontrado.';
$string['deletecurriculumconfirm'] = 'Excluir este programa?';
$string['deletecurriculumtitle'] = 'Excluir programa?';
$string['deletecurriculumbody'] = 'Isso excluirá "{$a}".';
$string['curriculumdeleted'] = 'Programa excluído.';
$string['curriculumdeletecourses'] = 'Não é possível excluir um programa que possui disciplinas. Remova todas as disciplinas primeiro.';
$string['nomappings'] = 'Nenhum mapeamento encontrado.';
$string['csvfile'] = 'Arquivo CSV';
$string['upload'] = 'Enviar';
$string['courseidrequired'] = 'O ID do curso é obrigatório para visualizar o progresso.';
$string['mappedcourse'] = 'Disciplina mapeada';
$string['externalcourse'] = 'Disciplina externa';
$string['program'] = 'Programa';
$string['enrolled'] = 'Inscrito';
$string['progressbyenrollment'] = 'por inscrição';
$string['progressbycompletion'] = 'por conclusão';
$string['notenrolled'] = 'Não inscrito';
$string['moodlecoursesforexternal'] = 'Cursos Moodle para {$a}';
$string['close'] = 'Fechar';
