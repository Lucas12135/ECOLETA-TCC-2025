-- Adicionar coluna experiencia na tabela coletores
ALTER TABLE coletores
ADD COLUMN experiencia TEXT AFTER meio_transporte;