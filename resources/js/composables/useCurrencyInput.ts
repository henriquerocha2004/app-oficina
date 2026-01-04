import { ref, watch } from 'vue';

export function useCurrencyInput(initialValue: number = 0) {
    const displayValue = ref('');
    const numericValue = ref(initialValue);

    // Formata número para exibição
    const formatCurrency = (value: number): string => {
        return new Intl.NumberFormat('pt-BR', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        }).format(value);
    };

    // Remove formatação e converte para número
    const parseInput = (value: string): number => {
        // Remove tudo exceto números e vírgula
        const cleaned = value.replace(/[^\d,]/g, '');
        // Substitui vírgula por ponto
        const withDot = cleaned.replace(',', '.');
        const parsed = parseFloat(withDot);
        return isNaN(parsed) ? 0 : parsed;
    };

    // Atualiza o display quando o valor numérico muda
    watch(numericValue, (newValue) => {
        displayValue.value = formatCurrency(newValue);
    }, { immediate: true });

    // Handler para input do usuário
    const handleInput = (event: Event) => {
        const input = event.target as HTMLInputElement;
        const cursorPosition = input.selectionStart || 0;
        const oldLength = displayValue.value.length;

        // Pega o valor digitado
        const inputValue = input.value;
        
        // Parse para número
        numericValue.value = parseInput(inputValue);
        
        // Formata
        const formatted = formatCurrency(numericValue.value);
        displayValue.value = formatted;

        // Ajusta posição do cursor
        const newLength = formatted.length;
        const diff = newLength - oldLength;
        const newPosition = Math.max(0, Math.min(cursorPosition + diff, newLength));
        
        // Restaura posição do cursor no próximo tick
        setTimeout(() => {
            input.setSelectionRange(newPosition, newPosition);
        }, 0);
    };

    // Handler para blur - garante formatação final
    const handleBlur = () => {
        displayValue.value = formatCurrency(numericValue.value);
    };

    // Seta um novo valor programaticamente
    const setValue = (value: number) => {
        numericValue.value = value;
    };

    return {
        displayValue,
        numericValue,
        handleInput,
        handleBlur,
        setValue,
    };
}
