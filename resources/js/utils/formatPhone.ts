export default function formatPhone(phone: string): string {
    phone = phone.replace(/\D/g, '')

    if (phone.length > 11) phone = phone.slice(0, 11)

    if (phone.length <= 10) {
        return phone.replace(/(\d{2})(\d{4})(\d{0,4})/, '($1) $2-$3')
    }

    return phone.replace(/(\d{2})(\d{5})(\d{0,4})/, '($1) $2-$3')
}