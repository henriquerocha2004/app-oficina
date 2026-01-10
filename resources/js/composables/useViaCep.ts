import { ref } from 'vue';

interface ViaCepResponse {
    cep: string;
    logradouro: string;
    complemento: string;
    bairro: string;
    localidade: string;
    uf: string;
    erro?: boolean;
}

export function useViaCep() {
    const isLoading = ref(false);
    const error = ref<string | null>(null);

    const fetchAddress = async (zipcode: string): Promise<ViaCepResponse | null> => {
        const cleaned = zipcode.replace(/\D/g, '');
        
        if (cleaned.length !== 8) {
            return null;
        }

        isLoading.value = true;
        error.value = null;

        try {
            const response = await fetch(`https://viacep.com.br/ws/${cleaned}/json/`);
            const data = await response.json();

            if (data.erro) {
                error.value = 'CEP não encontrado';
                return null;
            }

            return data;
        } catch (err) {
            error.value = 'Erro ao buscar CEP';
            console.error('Error fetching address:', err);
            return null;
        } finally {
            isLoading.value = false;
        }
    };

    return {
        fetchAddress,
        isLoading,
        error,
    };
}
