<h3>1) @lang('Objeto del Documento')</h3>
<p>
    @lang(':app es un sistema de gestión de archivos de confianza que procesa eventos de firma digital avanzada,
        compartición de archivos, solicitud de documentos, verificación de datos, entre otros servicios. Se puede
        acceder a su plataforma a través de la dirección <a href=":url">:url</a>',
        [
            'app' => config('app.name'),
            'url' => config('app.url'),
        ]
    )
</p>
<p>
    @lang(':name, en nombre y representación de la sociedad :company, con domicilio social en :street :zip :city
        :country, inscrita en el :register, en el Tomo :volume, Sección :section, Folio :invoice, Hoja :page,
        Inscripción :inscription y con CIF :cif; actúa en su calidad de Administrador de la Sociedad con poderes plenos,
        tal como tiene acreditado en virtud de escritura del Notario de :notary-city, :notary-name, de fecha :date y
        número de registro :number, que resultó inscrita en el Registro Mercantil de :register, Tomo :volume, Sección
        :section, Folio :invoice, Hoja :page, Inscripción :inscription.',
            [
                'name'          => config('certified.representation.name'),
                'company'       => @config('certified.name'),
                'cif'           => @config('certified.cif'),
                'street'        => @config('certified.street'),
                'zip'           => @config('certified.zip'),
                'city'          => @config('certified.city'),
                'country'       => @config('certified.country'),
                'notary-name'   => @config('certified.record.notary.name'),
                'notary-city'   => @config('certified.record.notary.city'),
                'register'      => @config('certified.record.register'),
                'date'          => @config('certified.record.date'),
                'record'        => @config('certified.record.number'),
                'volume'        => @config('certified.record.volume'),
                'section'       => @config('certified.record.section'),
                'invoice'       => @config('certified.record.invoice'),
                'page'          => @config('certified.record.page'),
                'inscription'   => @config('certified.record.inscription'),
                'number'        => @config('certified.record.number'),
            ]
    )
</p>
@yield('document-goals')
