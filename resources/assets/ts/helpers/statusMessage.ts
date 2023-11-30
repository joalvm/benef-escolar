function statusMessage(status: string, short = false): string {
    let message = '';

    switch (status) {
        case 'observed':
            message = short ? 'Observado' : 'Modificar su información requerida';
            break;
        case 'approved':
            message = short ? 'Aprobado' : 'Solicitud aprobada por el responsable GH';
            break;
        case 'no_requested':
            message = short ? 'No Solicitado' : 'Debes adjuntar los formatos de préstamo';
            break;
        default:
            message = short ? 'Pendiente de Revisión' : 'El responsable GH está revisando su información';
            break;
    }

    return message;
}

window.statusMessage = statusMessage;

export default statusMessage;
