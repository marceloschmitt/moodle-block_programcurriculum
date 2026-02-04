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
$string['programcurriculum:viewprogress'] = 'Ver próprio progresso no currículo';
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
$string['courses'] = 'Cursos';
$string['nocourses'] = 'Nenhum curso encontrado.';
$string['addcourse'] = 'Adicionar curso';
$string['editcourse'] = 'Editar curso';
$string['coursename'] = 'Nome do curso';
$string['coursecode'] = 'Código do curso';
$string['duplicatecoursecode'] = 'Este código de curso já está em uso.';
$string['deletecoursebody'] = 'Isso excluirá "{$a}".';
$string['deletecoursetitle'] = 'Excluir curso?';
$string['deletecourseconfirm'] = 'Excluir este curso?';
$string['coursedeleted'] = 'Curso excluído.';
$string['coursedeletemappings'] = 'Você não pode excluir um curso que possui mapeamentos.';
$string['courseformerror'] = 'Por favor, corrija os erros no formulário do curso.';
$string['curriculumformerror'] = 'Por favor, corrija os erros no formulário do currículo.';
$string['duplicatecurriculumnameorcode'] = 'O nome ou código do programa já existe. Por favor, escolha valores diferentes.';
$string['sortorder'] = 'Ordem';
$string['moveto'] = 'Mover para posição';
$string['movemodaltitle'] = 'Mover curso';
$string['movemodaltitlewithname'] = 'Mover curso: {$a}';
$string['movepositionhelp'] = 'Informe uma posição de 1 a {$a}.';
$string['movepositioninvalid'] = 'A posição deve estar entre 1 e o último item.';
$string['listofcourses'] = 'Lista de cursos';
$string['backtoprograms'] = 'Voltar aos programas';
$string['mappings'] = 'Mapeamentos';
$string['addmapping'] = 'Adicionar mapeamento';
$string['manualmapping'] = 'Mapeamento manual';
$string['automaticmapping'] = 'Mapeamento automático';
$string['automaticmappingdone'] = 'Mapeamento automático concluído. {$a} mapeamento(s) criado(s).';
$string['automaticmappingnocode'] = 'O curso externo não possui código. Não é possível executar o mapeamento automático.';
$string['automaticmappingdonecurriculum'] = 'Mapeamento automático concluído. {$a} mapeamento(s) criado(s) para os cursos do currículo.';
$string['editmapping'] = 'Editar requisito';
$string['move'] = 'Mover';
$string['deletemappingconfirm'] = 'Excluir este mapeamento?';
$string['deletemappingbody'] = 'Isso excluirá "{$a}".';
$string['deletemappingtitle'] = 'Excluir mapeamento?';
$string['mappingdeleted'] = 'Mapeamento excluído.';
$string['mappedmoodlecourse'] = 'Curso Moodle mapeado';
$string['mappingofcourse'] = 'Mapeamento do curso externo {$a}';
$string['listofmappings'] = 'Lista de mapeamentos';
$string['listofmappingsforcourse'] = 'Lista de mapeamentos - {$a}';
$string['backtocourses'] = 'Voltar aos cursos';
$string['mappingformerror'] = 'Por favor, corrija os erros no formulário de mapeamento.';
$string['duplicatemapping'] = 'Este curso Moodle já está mapeado para este curso externo.';
$string['courseplaceholder'] = 'Digite para buscar um curso';
$string['importtitle'] = 'Importar CSV de currículo';
$string['importhelp'] = 'Colunas do CSV: curriculum_code, curriculum_name, course_code, course_name, moodle_course_id, sortorder, curriculum_description';
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
$string['nomappings'] = 'Nenhum mapeamento encontrado.';
$string['csvfile'] = 'Arquivo CSV';
$string['upload'] = 'Enviar';
$string['courseidrequired'] = 'O ID do curso é obrigatório para visualizar o progresso.';
$string['mappedcourse'] = 'Curso mapeado';
$string['externalcourse'] = 'Curso externo';
$string['program'] = 'Programa';
$string['enrolled'] = 'Inscrito';
$string['progressbyenrollment'] = 'por inscrição';
$string['progressbycompletion'] = 'por conclusão';
$string['notenrolled'] = 'Não inscrito';
$string['moodlecoursesforexternal'] = 'Cursos Moodle para {$a}';
$string['close'] = 'Fechar';
