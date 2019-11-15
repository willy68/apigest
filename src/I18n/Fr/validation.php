<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | such as the size rules. Feel free to tweak each of these messages.
    |
    */

    'accepted'             => 'Le champ %1$s doit être accepté.',
    'active_url'           => 'Le champ %1$s n\'est pas une URL valide.',
    'after'                => 'Le champ %1$s doit être une date postérieure au :date.',
    'alpha'                => 'Le champ %1$s doit seulement contenir des lettres.',
    'alpha_dash'           => 'Le champ %1$s doit seulement contenir des lettres, des chiffres et des tirets.',
    'alpha_num'            => 'Le champ %1$s doit seulement contenir des chiffres et des lettres.',
    'array'                => 'Le champ %1$s doit être un tableau.',
    'before'               => 'Le champ %1$s doit être une date antérieure au :date.',
    'range'              => [
        'numeric' => 'La valeur de %1$s doit être comprise entre %2$d et %3$d.',
        'file'    => 'Le fichier %1$s doit avoir une taille entre %2$d et %3$d kilo-octets.',
        'string'  => 'Le texte %1$s doit avoir entre %2$d et %3$d caractères.',
        'array'   => 'Le tableau %1$s doit avoir entre %2$d et %3$d éléments.',
    ],
    'boolean'              => 'Le champ %1$s doit être vrai ou faux.',
    'confirmed'            => 'Le champ de confirmation %1$s ne correspond pas.',
    'date'                 => 'Le champ %1$s n\'est pas une date valide, format valide: %2$s',
    'date_format'          => 'Le champ %1$s ne correspond pas au format :format.',
    'different'            => 'Les champs %1$s et :other doivent être différents.',
    'digits'               => 'Le champ %1$s doit avoir :digits chiffres.',
    'digits_between'       => 'Le champ %1$s doit avoir entre %2$d et %3$d chiffres.',
    'distinct'             => 'Le champ %1$s a une valeur dupliquée.',
    'email'                => 'Le champ %1$s doit être une adresse E-mail valide.',
    'exists'               => 'Le champ %1$s sélectionné est invalide.',
    'filled'               => 'Le champ %1$s est obligatoire.',
    'image'                => 'Le champ %1$s doit être une image.',
    'in'                   => 'Le champ %1$s est invalide.',
    'in_array'             => 'Le champ %1$s n\'existe pas dans :other.',
    'integer'              => 'Le champ %1$s doit être un entier.',
    'ip'                   => 'Le champ %1$s doit être une adresse IP valide.',
    'json'                 => 'Le champ %1$s doit être un document JSON valide.',
    'max'                  => [
        'numeric' => 'La valeur de %1$s ne peut être supérieure à %3$d.',
        'file'    => 'Le fichier %1$s ne peut être plus gros que %3$d kilo-octets.',
        'string'  => 'Le texte de %1$s ne peut contenir plus de %3$d caractères.',
        'array'   => 'Le tableau %1$s ne peut avoir plus de %3$d éléments.',
    ],
    'mimes'                => 'Le champ %1$s doit être un fichier de type : :values.',
    'min'                  => [
        'numeric' => 'La valeur de %1$s doit être supérieure à %2$d.',
        'file'    => 'Le fichier %1$s doit être plus gros que %2$d kilo-octets.',
        'string'  => 'Le texte %1$s doit contenir au moins %2$d caractères.',
        'array'   => 'Le tableau %1$s doit avoir au moins %2$d éléments.',
    ],
    'not_in'               => 'Le champ %1$s sélectionné n\'est pas valide.',
    'numeric'              => 'Le champ %1$s doit contenir un nombre.',
    'present'              => 'Le champ %1$s doit être présent.',
    'regex'                => 'Le format du champ %1$s est invalide.',
    'required'             => 'Le champ %1$s est obligatoire.',
    'required_if'          => 'Le champ %1$s est obligatoire quand la valeur de :other est :value.',
    'required_unless'      => 'Le champ %1$s est obligatoire sauf si :other est :values.',
    'required_with'        => 'Le champ %1$s est obligatoire quand :values est présent.',
    'required_with_all'    => 'Le champ %1$s est obligatoire quand :values est présent.',
    'required_without'     => 'Le champ %1$s est obligatoire quand :values n\'est pas présent.',
    'required_without_all' => 'Le champ %1$s est requis quand aucun de :values n\'est présent.',
    'same'                 => 'Les champs %1$s et :other doivent être identiques.',
    'size'                 => [
        'numeric' => 'La valeur de %1$s doit être :size.',
        'file'    => 'La taille du fichier de %1$s doit être de :size kilo-octets.',
        'string'  => 'Le texte de %1$s doit contenir :size caractères.',
        'array'   => 'Le tableau %1$s doit contenir :size éléments.',
    ],
    'string'               => 'Le champ %1$s doit être une chaîne de caractères.',
    'timezone'             => 'Le champ %1$s doit être un fuseau horaire valide.',
    'unique'               => 'La valeur du champ %1$s est déjà utilisée.',
    'url'                  => 'Le format de l\'URL de %1$s n\'est pas valide.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom'               => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap attribute place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of "email". This simply helps us make messages a little cleaner.
    |
    */

    'attributes'           => [
        'name'                  => 'Nom',
        'username'              => 'Pseudo',
        'email'                 => 'E-mail',
        'first_name'            => 'Prénom',
        'last_name'             => 'Nom',
        'password'              => 'Mot de passe',
        'password_confirmation' => 'Confirmation du mot de passe',
        'city'                  => 'Ville',
        'country'               => 'Pays',
        'address'               => 'Adresse',
        'phone'                 => 'Téléphone',
        'mobile'                => 'Portable',
        'age'                   => 'Age',
        'sex'                   => 'Sexe',
        'gender'                => 'Genre',
        'day'                   => 'Jour',
        'month'                 => 'Mois',
        'year'                  => 'Année',
        'hour'                  => 'Heure',
        'minute'                => 'Minute',
        'second'                => 'Seconde',
        'title'                 => 'Titre',
        'content'               => 'Contenu',
        'description'           => 'Description',
        'excerpt'               => 'Extrait',
        'date'                  => 'Date',
        'time'                  => 'Heure',
        'available'             => 'Disponible',
        'size'                  => 'Taille',
    ],

];
