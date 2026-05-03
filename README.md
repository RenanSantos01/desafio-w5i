 Sistema de Controle de Atendimentos - Desafio W5i:

Tecnologias e Ferramentas Utilizadas:

 Backend: PHP (Arquitetura MVC - Model, View, Controller)
 Banco de Dados:** MySQL (PDO para conexão segura)
 Gerenciador de Banco de Dados:** MySQL Workbench
 Servidor Local: XAMPP (Apache + MySQL)
 Frontend: HTML, CSS in-line e Bootstrap 5 

 Estrutura do Projeto:

O projeto foi organizado no padrão MVC para separar a lógica de negócio da interface e do banco de dados, contendo as pastas `config`, `controllers`, `models` e `public`.

 Como executar o projeto:

1. Configuração do Servidor Local (XAMPP):
    Certifique-se de ter o **XAMPP** instalado.
    Coloque a pasta raiz do projeto (nomeada como `desafio-w5i`) dentro do diretório `htdocs` do seu XAMPP (geralmente em `C:\xampp\htdocs\desafio-w5i`).
    Abra o painel de controle do XAMPP e inicie os serviços **Apache** e **MySQL**.

2. Configuração do Banco de Dados:
    Abra o **MySQL Workbench** e conecte-se à sua instância local.
    Abra e execute o arquivo script `database.sql` (localizado na raiz deste projeto).
    Este script criará o banco de dados `controle_atendimentos`, as tabelas com seus relacionamentos e fará a inserção dos dados iniciais (Setores e Prioridades).
    Aviso: Verifique o arquivo `config/database.php` e ajuste as credenciais de acesso ao seu MySQL (usuário e senha), se necessário.

3. Acessando o Sistema:
    Com o Apache rodando, abra o seu navegador e acesse a seguinte URL:
     [http://localhost/desafio-w5i/public/index.php](http://localhost/desafio-w5i/public/index.php)

 Diferenciais Implementados: 
 
 Dashboard Resumo:** Cards dinâmicos mostrando a quantidade de chamados em cada status.
 
 Interface Moderna e UX: Ações de iniciar e finalizar chamados integradas diretamente na tabela.
 
 Segurança: Uso de `PDO` com `bindParam` para prevenção total contra ataques de SQL Injection.
 
 Integridade de Dados: Banco de dados modelado com restrições de chaves estrangeiras (`FOREIGN KEY`) e campos obrigatórios (`NOT NULL`).

---
Desenvolvido por Renan Cardoso Portela dos Santos.