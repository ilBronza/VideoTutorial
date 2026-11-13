<?php

$prefix = (string) config('videotutorial.routePrefix');

return [
    $prefix . 'frontend.videotutorials.index' => 'Mostra il catalogo dei videotutorial con la relativa gerarchia.',
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
