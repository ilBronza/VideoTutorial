<?php

$prefix = (string) config('videotutorial.routePrefix');

return [
    $prefix . 'contextualUpload.store' => 'Carica un video e crea una bozza associata alla pagina corrente.',
    $prefix . 'contextualUpload.edit' => 'Mostra il modulo di modifica di un videotutorial caricato dall’utente.',
    $prefix . 'contextualUpload.update' => 'Salva i dati e la visibilità di un videotutorial caricato dall’utente.',
    $prefix . 'frontend.videotutorials.index' => 'Mostra il catalogo dei videotutorial con la relativa gerarchia.',
    $prefix . 'frontend.videotutorials.show' => 'Mostra la scheda di un videotutorial pubblicato.',
    $prefix . 'videotutorials.reorder' => 'Mostra la schermata per riordinare i videotutorial.',
    $prefix . 'videotutorials.storeReorder' => 'Salva l’ordine e la gerarchia dei videotutorial.',
    $prefix . 'videotutorials.deleteMedia' => 'Elimina un allegato del videotutorial.',
    $prefix . 'videotutorials.index' => 'Mostra l’elenco dei videotutorial.',
    $prefix . 'videotutorials.create' => 'Mostra il modulo per creare un videotutorial.',
    $prefix . 'videotutorials.store' => 'Salva un nuovo videotutorial.',
    $prefix . 'videotutorials.show' => 'Mostra i dettagli del videotutorial.',
    $prefix . 'videotutorials.edit' => 'Mostra il modulo di modifica del videotutorial.',
    $prefix . 'videotutorials.update' => 'Salva le modifiche del videotutorial.',
    $prefix . 'videotutorials.destroy' => 'Elimina un videotutorial.',
];
