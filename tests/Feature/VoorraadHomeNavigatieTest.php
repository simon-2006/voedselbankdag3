<?php

it('toont op het productvoorraad overzicht een home knop die naar home navigeert', function () {
    $response = $this->view('voorraad.index', [
        'categorieen' => [],
        'gekozenCategorie' => null,
        'voorraadProducten' => collect(),
        'foutmelding' => null,
    ]);

    $response->assertSee(
        '<a href="'.route('home').'" class="btn btn-primary text-white">Home</a>',
        false
    );
});
