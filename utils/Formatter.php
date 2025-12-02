<?php

class Formatter {
    
    /**
     * Formata CPF no padrao 000.000.000-00
     */
    public static function formatarCPF(?string $cpf): string {
        if (empty($cpf)) {
            return '-';
        }
        
        $cpf = preg_replace('/[^0-9]/', '', $cpf);
        
        if (strlen($cpf) != 11) {
            return $cpf;
        }
        
        return substr($cpf, 0, 3) . '.' . 
               substr($cpf, 3, 3) . '.' . 
               substr($cpf, 6, 3) . '-' . 
               substr($cpf, 9, 2);
    }
    
    /**
     * Formata telefone no padrao (00) 00000-0000
     */
    public static function formatarTelefone(?string $telefone): string {
        if (empty($telefone)) {
            return '-';
        }
        
        $telefone = preg_replace('/[^0-9]/', '', $telefone);
        
        // Celular (11 dígitos)
        if (strlen($telefone) == 11) {
            return '(' . substr($telefone, 0, 2) . ') ' . 
                   substr($telefone, 2, 5) . '-' . 
                   substr($telefone, 7, 4);
        }
        
        // Fixo (10 dígitos)
        if (strlen($telefone) == 10) {
            return '(' . substr($telefone, 0, 2) . ') ' . 
                   substr($telefone, 2, 4) . '-' . 
                   substr($telefone, 6, 4);
        }
        
        return $telefone;
    }
    
    /**
     * Formata CEP no padrao 00000-000
     */
    public static function formatarCEP(?string $cep): string {
        if (empty($cep)) {
            return '-';
        }
        
        $cep = preg_replace('/[^0-9]/', '', $cep);
        
        if (strlen($cep) != 8) {
            return $cep;
        }
        
        return substr($cep, 0, 5) . '-' . substr($cep, 5, 3);
    }
    
    /**
     * Formata data no padrao brasileiro dd/mm/yyyy
     */
    public static function formatarData(?string $data): string {
        if (empty($data)) {
            return '-';
        }
        
        $timestamp = strtotime($data);
        if ($timestamp === false) {
            return $data;
        }
        
        return date('d/m/Y', $timestamp);
    }
    
    /**
     * Formata data e hora no padrao brasileiro dd/mm/yyyy HH:mm
     */
    public static function formatarDataHora(?string $data): string {
        if (empty($data)) {
            return '-';
        }
        
        $timestamp = strtotime($data);
        if ($timestamp === false) {
            return $data;
        }
        
        return date('d/m/Y H:i', $timestamp);
    }
    
    /**
     * Formata valor monetario no padrao brasileiro
     */
    public static function formatarDinheiro(?float $valor): string {
        if ($valor === null) {
            return 'R$ 0,00';
        }
        
        return 'R$ ' . number_format($valor, 2, ',', '.');
    }
    
    /**
     * Remove formatacao de CPF
     */
    public static function limparCPF(?string $cpf): ?string {
        if (empty($cpf)) {
            return null;
        }
        
        return preg_replace('/[^0-9]/', '', $cpf);
    }
    
    /**
     * Remove formatacao de telefone
     */
    public static function limparTelefone(?string $telefone): ?string {
        if (empty($telefone)) {
            return null;
        }
        
        return preg_replace('/[^0-9]/', '', $telefone);
    }
    
    /**
     * Remove formatacao de CEP
     */
    public static function limparCEP(?string $cep): ?string {
        if (empty($cep)) {
            return null;
        }
        
        return preg_replace('/[^0-9]/', '', $cep);
    }
    
    /**
     * Trunca texto com reticências
     */
    public static function truncar(?string $texto, int $limite = 50): string {
        if (empty($texto)) {
            return '-';
        }
        
        if (strlen($texto) <= $limite) {
            return $texto;
        }
        
        return substr($texto, 0, $limite) . '...';
    }
}
?>