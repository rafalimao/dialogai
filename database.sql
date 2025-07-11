-- ============================================
-- Sistema de Administração de Assistentes
-- Script de Criação do Banco de Dados
-- ============================================

-- Criar banco de dados (opcional - descomente se necessário)
-- CREATE DATABASE assistant_admin CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- USE assistant_admin;

-- ============================================
-- Tabela: assistants
-- ============================================
CREATE TABLE IF NOT EXISTS `assistants` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `profile` TEXT NOT NULL COMMENT 'Descrição do perfil e características do assistente',
    `initial_prompt` TEXT NOT NULL COMMENT 'Prompt inicial para o assistente',
    `goal` TEXT NOT NULL COMMENT 'Objetivo final que o assistente busca na conversa',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_name` (`name`),
    INDEX `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Tabela: conversations
-- ============================================
CREATE TABLE IF NOT EXISTS `conversations` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `assistant1_id` INT NOT NULL,
    `assistant2_id` INT NOT NULL,
    `start_time` DATETIME NOT NULL,
    `end_time` DATETIME NULL,
    `final_agreement` TEXT NULL COMMENT 'Acordo final alcançado na conversa',
    `status` ENUM('in_progress', 'completed', 'failed') DEFAULT 'in_progress',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`assistant1_id`) REFERENCES `assistants`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`assistant2_id`) REFERENCES `assistants`(`id`) ON DELETE CASCADE,
    INDEX `idx_assistant1` (`assistant1_id`),
    INDEX `idx_assistant2` (`assistant2_id`),
    INDEX `idx_status` (`status`),
    INDEX `idx_start_time` (`start_time`),
    INDEX `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Tabela: messages
-- ============================================
CREATE TABLE IF NOT EXISTS `messages` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `conversation_id` INT NOT NULL,
    `sender_id` INT NOT NULL COMMENT 'ID do assistente que enviou a mensagem',
    `recipient_id` INT NOT NULL COMMENT 'ID do assistente que recebeu a mensagem',
    `content` TEXT NOT NULL,
    `timestamp` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`conversation_id`) REFERENCES `conversations`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`sender_id`) REFERENCES `assistants`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`recipient_id`) REFERENCES `assistants`(`id`) ON DELETE CASCADE,
    INDEX `idx_conversation` (`conversation_id`),
    INDEX `idx_sender` (`sender_id`),
    INDEX `idx_recipient` (`recipient_id`),
    INDEX `idx_timestamp` (`timestamp`),
    INDEX `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Dados de Exemplo
-- ============================================

-- Inserir assistentes de exemplo
INSERT INTO `assistants` (`name`, `profile`, `initial_prompt`, `goal`) VALUES
(
    'Alice - Assistente Comercial',
    'Alice é uma assistente especializada em vendas e negociações. Ela é persuasiva, empática e sempre busca encontrar soluções que beneficiem ambas as partes. Tem experiência em diversos setores e conhece bem as técnicas de fechamento de vendas.',
    'Olá! Sou Alice, sua assistente comercial. Estou aqui para ajudar você a alcançar seus objetivos de vendas e construir relacionamentos duradouros com clientes. Como posso ajudá-lo hoje?',
    'Maximizar as vendas e a satisfação do cliente através de negociações eficazes e relacionamentos sólidos.'
),
(
    'Bob - Assistente Técnico',
    'Bob é um assistente técnico especializado em resolver problemas complexos e fornecer suporte técnico detalhado. Ele é analítico, preciso e tem vasta experiência em tecnologia, programação e sistemas.',
    'Olá! Sou Bob, seu assistente técnico. Estou pronto para ajudá-lo com qualquer desafio técnico, desde debugging de código até arquitetura de sistemas. Qual problema podemos resolver juntos?',
    'Resolver problemas técnicos de forma eficiente e educar os usuários sobre as melhores práticas tecnológicas.'
),
(
    'Carol - Assistente de Atendimento',
    'Carol é uma assistente focada em atendimento ao cliente e suporte. Ela é paciente, compreensiva e sempre busca resolver os problemas dos clientes da melhor forma possível. Tem excelentes habilidades de comunicação.',
    'Olá! Sou Carol, sua assistente de atendimento. Estou aqui para garantir que você tenha a melhor experiência possível. Como posso ajudá-lo hoje?',
    'Proporcionar um atendimento excepcional e resolver todas as questões dos clientes com eficiência e empatia.'
),
(
    'David - Assistente de Marketing',
    'David é um assistente especializado em marketing digital e estratégias de crescimento. Ele é criativo, analítico e sempre busca maneiras inovadoras de promover produtos e serviços.',
    'Olá! Sou David, seu assistente de marketing. Vamos criar campanhas incríveis e estratégias que realmente convertem. Qual é o seu próximo projeto?',
    'Desenvolver estratégias de marketing eficazes que aumentem a visibilidade da marca e gerem resultados mensuráveis.'
);

-- Inserir conversas de exemplo
INSERT INTO `conversations` (`assistant1_id`, `assistant2_id`, `start_time`, `end_time`, `final_agreement`, `status`) VALUES
(
    1, 2, 
    DATE_SUB(NOW(), INTERVAL 2 HOUR), 
    DATE_SUB(NOW(), INTERVAL 1 HOUR),
    'Acordo estabelecido para implementar uma solução técnica que atenda às necessidades comerciais, com prazo de entrega de 15 dias e suporte técnico incluso.',
    'completed'
),
(
    3, 4, 
    DATE_SUB(NOW(), INTERVAL 4 HOUR), 
    DATE_SUB(NOW(), INTERVAL 3 HOUR),
    'Definida estratégia de marketing focada em melhorar o atendimento ao cliente, incluindo campanhas de feedback e programa de fidelidade.',
    'completed'
),
(
    1, 3, 
    DATE_SUB(NOW(), INTERVAL 1 HOUR), 
    NULL,
    NULL,
    'in_progress'
),
(
    2, 4, 
    DATE_SUB(NOW(), INTERVAL 6 HOUR), 
    DATE_SUB(NOW(), INTERVAL 5 HOUR),
    NULL,
    'failed'
);

-- Inserir mensagens de exemplo
INSERT INTO `messages` (`conversation_id`, `sender_id`, `recipient_id`, `content`, `timestamp`) VALUES
-- Conversa 1: Alice (Comercial) e Bob (Técnico)
(
    1, 1, 2,
    'Olá Bob! Tenho um cliente interessado em uma solução técnica customizada. Ele precisa de um sistema que integre com o CRM atual e tenha relatórios em tempo real. Você pode me ajudar a entender a viabilidade técnica?',
    DATE_SUB(NOW(), INTERVAL 2 HOUR)
),
(
    1, 2, 1,
    'Oi Alice! Claro, posso ajudar. A integração com CRM é totalmente viável. Precisamos saber qual CRM ele usa e que tipo de dados ele quer nos relatórios. Com essas informações, posso estimar o tempo de desenvolvimento.',
    DATE_SUB(NOW(), INTERVAL 119 MINUTE)
),
(
    1, 1, 2,
    'Perfeito! Ele usa Salesforce e quer relatórios de vendas, pipeline e performance da equipe. O orçamento dele é de até R$ 50.000. Conseguimos fazer algo nessa faixa?',
    DATE_SUB(NOW(), INTERVAL 115 MINUTE)
),
(
    1, 2, 1,
    'Com Salesforce fica mais fácil, eles têm uma API excelente. Para esse escopo, estimaria R$ 35.000 para desenvolvimento e R$ 5.000 para suporte nos primeiros 6 meses. Prazo de 15 dias úteis. O que acha?',
    DATE_SUB(NOW(), INTERVAL 110 MINUTE)
),
(
    1, 1, 2,
    'Excelente! Isso fica dentro do orçamento e o prazo é adequado. Vou apresentar a proposta para o cliente. Você pode me enviar um resumo técnico que eu possa incluir na apresentação?',
    DATE_SUB(NOW(), INTERVAL 105 MINUTE)
),
(
    1, 2, 1,
    'Claro! Vou preparar um documento técnico detalhado com arquitetura, tecnologias utilizadas e cronograma. Envio em 2 horas. Obrigado pela parceria, Alice!',
    DATE_SUB(NOW(), INTERVAL 100 MINUTE)
),

-- Conversa 2: Carol (Atendimento) e David (Marketing)
(
    2, 3, 4,
    'Oi David! Estou recebendo muitas reclamações sobre demora no atendimento. Os clientes estão insatisfeitos e isso está afetando nossa reputação. Você tem alguma ideia de como podemos melhorar isso através do marketing?',
    DATE_SUB(NOW(), INTERVAL 4 HOUR)
),
(
    2, 4, 3,
    'Oi Carol! Essa é uma oportunidade de ouro para transformar um problema em vantagem competitiva. Podemos criar uma campanha de transparência, mostrando nossos esforços para melhorar. Que tal um programa de feedback ativo?',
    DATE_SUB(NOW(), INTERVAL 235 MINUTE)
),
(
    2, 3, 4,
    'Interessante! Como funcionaria esse programa de feedback? E você acha que devemos ser transparentes sobre os problemas atuais?',
    DATE_SUB(NOW(), INTERVAL 230 MINUTE)
),
(
    2, 4, 3,
    'Sim! Transparência gera confiança. Podemos criar uma página "Melhorias em Andamento" e um sistema de feedback em tempo real. Também sugiro um programa de fidelidade para clientes que tiveram problemas - transformar insatisfação em lealdade.',
    DATE_SUB(NOW(), INTERVAL 225 MINUTE)
),
(
    2, 3, 4,
    'Adorei a ideia! Isso mostra que nos importamos com a experiência deles. Como implementamos isso rapidamente?',
    DATE_SUB(NOW(), INTERVAL 220 MINUTE)
),
(
    2, 4, 3,
    'Vamos fazer um MVP em 1 semana: página de status, formulário de feedback e email automático para clientes afetados. Depois expandimos com o programa de fidelidade. Posso contar com seu time para os textos de atendimento?',
    DATE_SUB(NOW(), INTERVAL 215 MINUTE)
),
(
    2, 3, 4,
    'Perfeito! Meu time vai adorar participar. Vamos criar scripts de atendimento alinhados com a campanha. Obrigada pela parceria, David!',
    DATE_SUB(NOW(), INTERVAL 210 MINUTE)
),

-- Conversa 3: Alice (Comercial) e Carol (Atendimento) - Em andamento
(
    3, 1, 3,
    'Oi Carol! Preciso da sua ajuda com um cliente VIP. Ele está considerando cancelar o contrato devido a alguns problemas no atendimento. Como podemos reverter essa situação?',
    DATE_SUB(NOW(), INTERVAL 50 MINUTE)
),
(
    3, 3, 1,
    'Oi Alice! Vou verificar o histórico dele imediatamente. Qual é o nome do cliente e quais foram os problemas relatados? Podemos agendar uma ligação de reconciliação hoje mesmo.',
    DATE_SUB(NOW(), INTERVAL 45 MINUTE)
),
(
    3, 1, 3,
    'É a empresa TechCorp, contrato de R$ 200.000 anuais. Eles reclamaram de demora nas respostas e falta de proatividade. O CEO está muito insatisfeito. Você consegue uma solução especial para eles?',
    DATE_SUB(NOW(), INTERVAL 40 MINUTE)
),

-- Conversa 4: Bob (Técnico) e David (Marketing) - Falhou
(
    4, 2, 4,
    'David, preciso discutir a viabilidade técnica de uma campanha que você propôs. O sistema atual não suporta o volume de dados que você está prometendo aos clientes.',
    DATE_SUB(NOW(), INTERVAL 6 HOUR)
),
(
    4, 4, 2,
    'Bob, mas isso é fundamental para a campanha! Não podemos voltar atrás agora. Não existe uma solução técnica rápida?',
    DATE_SUB(NOW(), INTERVAL 355 MINUTE)
),
(
    4, 2, 4,
    'Entendo a urgência, mas seria irresponsável prometer algo que não podemos entregar. Precisamos de pelo menos 3 meses para fazer os upgrades necessários na infraestrutura.',
    DATE_SUB(NOW(), INTERVAL 350 MINUTE)
),
(
    4, 4, 2,
    'Três meses? Isso vai arruinar toda a estratégia de lançamento! Não existe mesmo uma alternativa?',
    DATE_SUB(NOW(), INTERVAL 345 MINUTE)
),
(
    4, 2, 4,
    'Lamento, mas não posso comprometer a estabilidade do sistema. Sugiro reformular a campanha com expectativas mais realistas.',
    DATE_SUB(NOW(), INTERVAL 340 MINUTE)
);

-- ============================================
-- Índices Adicionais para Performance
-- ============================================

-- Índice composto para consultas de conversas por assistente e status
CREATE INDEX `idx_conversations_assistant_status` ON `conversations` (`assistant1_id`, `assistant2_id`, `status`);

-- Índice para consultas de mensagens por conversa e timestamp
CREATE INDEX `idx_messages_conversation_timestamp` ON `messages` (`conversation_id`, `timestamp`);

-- Índice para consultas de estatísticas por data
CREATE INDEX `idx_conversations_date_status` ON `conversations` (`start_time`, `status`);
CREATE INDEX `idx_messages_date` ON `messages` (`timestamp`);

-- ============================================
-- Views para Consultas Frequentes
-- ============================================

-- View para estatísticas de conversas
CREATE OR REPLACE VIEW `conversation_stats` AS
SELECT 
    DATE(start_time) as date,
    status,
    COUNT(*) as count,
    AVG(TIMESTAMPDIFF(MINUTE, start_time, end_time)) as avg_duration_minutes
FROM conversations 
WHERE start_time >= DATE_SUB(NOW(), INTERVAL 30 DAY)
GROUP BY DATE(start_time), status
ORDER BY date DESC, status;

-- View para assistentes mais ativos
CREATE OR REPLACE VIEW `active_assistants` AS
SELECT 
    a.id,
    a.name,
    COUNT(DISTINCT c.id) as total_conversations,
    COUNT(DISTINCT CASE WHEN c.status = 'completed' THEN c.id END) as completed_conversations,
    COUNT(DISTINCT m.id) as total_messages,
    MAX(c.start_time) as last_conversation
FROM assistants a
LEFT JOIN conversations c ON (a.id = c.assistant1_id OR a.id = c.assistant2_id)
LEFT JOIN messages m ON (a.id = m.sender_id)
GROUP BY a.id, a.name
ORDER BY total_conversations DESC, total_messages DESC;

-- View para mensagens recentes com detalhes
CREATE OR REPLACE VIEW `recent_messages` AS
SELECT 
    m.id,
    m.content,
    m.timestamp,
    s.name as sender_name,
    r.name as recipient_name,
    c.id as conversation_id,
    c.status as conversation_status,
    CHAR_LENGTH(m.content) as character_count,
    CHAR_LENGTH(m.content) - CHAR_LENGTH(REPLACE(m.content, ' ', '')) + 1 as word_count
FROM messages m
JOIN assistants s ON m.sender_id = s.id
JOIN assistants r ON m.recipient_id = r.id
JOIN conversations c ON m.conversation_id = c.id
ORDER BY m.timestamp DESC;

-- ============================================
-- Triggers para Auditoria (Opcional)
-- ============================================

-- Trigger para atualizar timestamp de conversas quando mensagens são adicionadas
DELIMITER //
CREATE TRIGGER `update_conversation_timestamp` 
AFTER INSERT ON `messages`
FOR EACH ROW
BEGIN
    UPDATE conversations 
    SET updated_at = CURRENT_TIMESTAMP 
    WHERE id = NEW.conversation_id;
END//
DELIMITER ;

-- ============================================
-- Procedimentos Armazenados Úteis
-- ============================================

-- Procedimento para obter estatísticas gerais
DELIMITER //
CREATE PROCEDURE `GetSystemStats`()
BEGIN
    SELECT 
        (SELECT COUNT(*) FROM assistants) as total_assistants,
        (SELECT COUNT(*) FROM conversations) as total_conversations,
        (SELECT COUNT(*) FROM conversations WHERE status = 'in_progress') as active_conversations,
        (SELECT COUNT(*) FROM conversations WHERE status = 'completed') as completed_conversations,
        (SELECT COUNT(*) FROM conversations WHERE status = 'failed') as failed_conversations,
        (SELECT COUNT(*) FROM messages) as total_messages,
        (SELECT COUNT(*) FROM messages WHERE DATE(timestamp) = CURDATE()) as messages_today,
        (SELECT AVG(TIMESTAMPDIFF(MINUTE, start_time, end_time)) 
         FROM conversations 
         WHERE status = 'completed' AND end_time IS NOT NULL) as avg_duration_minutes;
END//
DELIMITER ;

-- ============================================
-- Comentários Finais
-- ============================================

-- Este script cria a estrutura completa do banco de dados para o
-- Sistema de Administração de Assistentes, incluindo:
-- 
-- 1. Tabelas principais com relacionamentos
-- 2. Índices para otimização de performance
-- 3. Dados de exemplo para demonstração
-- 4. Views para consultas frequentes
-- 5. Triggers para auditoria
-- 6. Procedimentos armazenados para estatísticas
--
-- Para usar este script:
-- 1. Crie um banco de dados MySQL
-- 2. Execute este script completo
-- 3. Configure as credenciais no arquivo config/database.php
-- 4. Acesse o painel administrativo
--
-- Versão: 1.0
-- Data: 2024

