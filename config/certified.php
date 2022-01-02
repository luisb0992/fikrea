<?php

/**
 * Configuración de la entidad certificadora
 *
 * Opciones de configuración de la entidad que emite los certificados
 *
 * @author javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos
 */

return
    [
        /*
        |--------------------------------------------------------------------------
        | Nombre de la entidad certificadora
        |--------------------------------------------------------------------------
        |
        */
        'name'  => 'Retail Servicios Externos SL',

        /*
        |--------------------------------------------------------------------------
        | El CIF de la entidad certificadora
        |--------------------------------------------------------------------------
        |
        */
        'cif'  => 'B8849732',

        /*
        |--------------------------------------------------------------------------
        | La dirección postal de la entidad certificadora
        |--------------------------------------------------------------------------
        |
        */
        'street'   => 'Calle Clara del Rey 27, 1D',

        /*
        |--------------------------------------------------------------------------
        | El código postal de la entidad certificadora
        |--------------------------------------------------------------------------
        |
        */
        'zip'      => '28006',

        /*
        |--------------------------------------------------------------------------
        | La localidad de la entidad certificadora
        |--------------------------------------------------------------------------
        |
        */
        'city'     => 'Madrid',

        /*
        |--------------------------------------------------------------------------
        | El país de la entidad certificadora
        |--------------------------------------------------------------------------
        |
        */
        'country'  => 'España',

        /*
        |--------------------------------------------------------------------------
        | Datos de registro de la entidad certificadora en el registro mercantil
        |--------------------------------------------------------------------------
        |
        */
        'record'  =>
            [
                /*
                |--------------------------------------------------------------------------
                | Fecha de constitución de la sociedad entidad certificadora
                |--------------------------------------------------------------------------
                |
                */
                'date' => '07-11-2019',

                /*
                |--------------------------------------------------------------------------
                | Nombre de la enidad registradora
                |--------------------------------------------------------------------------
                | El registro mercantil
                */
                'register' => 'Registro Mercantil de Madrid',

                /*
                |--------------------------------------------------------------------------
                | Nombre del notario
                |--------------------------------------------------------------------------
                | Nombre del notario y la ciudad
                */
                'notary'   =>
                    [
                        'name'      => 'D. LUIS ANGEL FERNANDEZ-REYES',
                        'city'      => 'VITORIA-GASTEIZ',
                    ],

                /*
                |--------------------------------------------------------------------------
                | Número de registro de la entidad
                |--------------------------------------------------------------------------
                |
                */
                'number'    => '2019/1737',

                /*
                |--------------------------------------------------------------------------
                | Tomo
                |--------------------------------------------------------------------------
                |
                */
                'volume'    => '39759',

                /*
                |--------------------------------------------------------------------------
                | Sección
                |--------------------------------------------------------------------------
                |
                */
                'section'    => '126',

                /*
                |--------------------------------------------------------------------------
                | Folio
                |--------------------------------------------------------------------------
                |
                */
                'invoice'    => '130',

                /*
                |--------------------------------------------------------------------------
                | Página
                |--------------------------------------------------------------------------
                |
                */
                'page'      => 'M-706460',

                /*
                |--------------------------------------------------------------------------
                | Inscripción
                |--------------------------------------------------------------------------
                |
                */
                'inscription' => '1',
            ],

        /*
        |--------------------------------------------------------------------------
        | La representación de la entidad certificadora
        |--------------------------------------------------------------------------
        |
        | Persona que reprsenta a la entidad certificadora a nivel legal
        */
        'representation'  =>
            [
                /*
                |--------------------------------------------------------------------------
                | El nombre y apellidos de la repesentación
                |--------------------------------------------------------------------------
                */
                'name'      =>  'D. MIKEL VALDERRAMA ALLENDE',

                /*
                |--------------------------------------------------------------------------
                | El Cif del representante legal
                |--------------------------------------------------------------------------
                */
                'cif'       => '72738956F',

                /*
                |--------------------------------------------------------------------------
                | El cargo que obstenta el representante legal
                |--------------------------------------------------------------------------
                */
                'position'  =>  'Director General',
                
                /*
                |--------------------------------------------------------------------------
                | La firma del representante legal
                |--------------------------------------------------------------------------
                */
                'sign'      =>  'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAOIAAABxCAYAAADI4n86AAAABmJLR0QA/wD/AP+gvaeTAAAiXElEQVR42u2d91ObV/r2L/Xee68gurHZbHazk5nv5G/P7Gw2k9gBISEkoV4e9d7L+8O+5+QBYwM2BIHP55dkJ2tZgufSfc5drluwXq/XYDAYz4rwud8Ag/GUkDizXq+v/fumIX7uN8BgPDXL5RLL5RLr9RoikQhC4f/ij0AgeO63RmFCZLxKSARcLpcYjUbodrtYr9dQq9VQq9WQSqXP/RavwYTIeHUQEc5mM/T7fVSrVVQqFYjFYng8HsjlckgkEggEAqzX642IjEyIjFcFuf/N53N0u10UCgVcXV2B4ziYTCaYzeZrd8RNECHAhMh4hcznc7TbbWQyGWQyGbTbbahUKlitVuh0Okgkkud+ix/BhMh4NazXaywWC7RaLRQKBeRyOfT7fej1egQCAbjdbhiNRnos3SSYEBkvHnLUXC6X6Pf7yGQyuLq6wmg0gtFohM/ng8fjgU6ng0wmg0Ag2Dghsjoi41WwXC4xGAxQKpWQTqfRaDSg0WgQCATg8/lgMpkgl8shFAo3ToQAi4iMV8BqtcJoNEK1WkU2m0Wn04FWq4Xf74fP54NWq72WJd1EBKzFjfFSWa/XVISFQgGZTAYcx0Gj0SAYDMLr9cJgMEAkEm1kFOTDIiLjRUJEOB6PUa/Xkc1mUa/XIZPJEAgEEAgEoNFoPivCTSpjMCEyXhykYD+dTtFoNJDNZsFxHIRCIZxOJ5xOJ9RqNcRi8a0CIyJeLBZYLBYQCASQSCSf/P//FTAhMl4URITj8RiNRgOZTAaFQgEKhQJerxcejwdGo/GzIlwulxgOh6jX6+h2uxCLxXA6nTAYDBCLn0cSTIiMFwWJhK1WC1dXV8jn81itVvB4PAgGg9Dr9ZBKpZ8UIfnzHMchFouhWCxCpVJBKpVCq9U+232SlS8YLwJynJzNZmi328jlckgmk+j1erBarXC73dDpdJ8UIWG5XKLdbiOZTOI///kPfv75Z0SjUbRaLSyXy2f7fCwiMl4E6/Wa9o9ms1lks1lMp1OYTCZ4vV6YTCYqws8JcTKZoFAoIBqNIhqNol6vQ6lUYj6fP2tpgwmRsfGQ1rVer4dSqYRsNovRaASXy4VgMAin03lnsZ5E02aziXg8jmg0inK5DJlMBqPRCJVKBZFI9GyfkR1NGY/OY0YWIsJOp0OPo7PZDC6XC7u7uwgEAnfe7Ug0rdfriEaj+O9//4toNIperwebzYbd3V1YrdZnrTeyiPgNcptQvuQBvPk6j/0Qkwwn6R/NZDIYDoew2+0IBoOw2+00kn0qOSMQCLBarTAcDlEoFGiCZjweQyQSwWw2w+v1Qq/X08n954AJ8RuB79eyXC4xm80wmUwAgGYNH9IMTepw8/kcACAWi2kb2WMIkrzPwWCAcrmMy8tLdDodOJ1OBAKBayL8FKSlbT6fo9lsIpfLIZ/PYzabQaFQ0Il9vV5Pj7bPBRPiN8RqtcJ8PsdgMECz2US5XIZAIEA4HIbNZrv3nB45Lna7XXQ6HaxWK+h0Ojpi9LXwa32VSgVXV1cYDAbQ6/Xwer1wOBx3ds2Q1yE1x0qlgnq9jsViQf+bVCqFSqWCQqF41mI+wIT46iEP43w+x2g0QqfTQaVSQaVSQalUglarhc1mg8ViedDrjcdjlEolFAoFSKVSeL1e2lz9pe+TH8EGgwE4jkMmk0G1WoVer0cwGITH44Farb7XfY5fc8zlcuj1enQweD6fQ6PRUP8a1uLGeBL4AhyPx2i326hWqyiVSiiVShiNRpDJZDCbzVCpVA86lpFoeHFxgaurK3g8HtjtdqxWqy9+v0SEi8UC7XYbxWIRV1dXaLVaUCqVCIfD8Pl80Ol094peRIS1Wg2JRAKZTAbL5RIWiwVqtRoSiQRWqxVWqxVyufy5f11MiK8RIsLJZIJWq4VKpYJyuYx6vY7BYID1eg2LxQK73Y5QKASdTndvIZIHnNy5qtUqHA7HNZvCL3m/AGgTd7lcRiaTQafTgUqlgtfrpQV7/nT9bcZP/CHhwWCAXC6HRCKB6XQKs9lMe1DlcjnMZjPMZjOLiIzHgf9Akg6U6XSKer2OZDKJQqFA73JarRZOpxN2ux0WiwUGgwEKheJeIiJ3N3LUK5fLmM1mUCqVUCqVX5XsWCwW6Pf7dLC31WpBq9XS6XqDwfCRxQX/34kASZaUfyTlOA5msxlutxvD4RCLxQJSqRR6vR4ajWYjPGyYEF8B5FhHMpmj0QiNRgOJRAKXl5dYrVYwm81wOBywWq2wWCzQarWQy+X0mHdX0gP4M2Kl02l8+PABHMfB5/PBbrdDr9c/+IEmr0vqhOl0Gn/88Qf6/T5cLhfC4TC8Xu9nW9fIl8NkMsF6vYZMJgMAdLtd5PN51Ot1KBQKBINBmEwmxONxDIdDCIVCKJVKKBSKZy3kE5gQXwH82bxOp4NSqUSHZIVCIVwuF/x+P5xOJ3Q6HeRyOT1K3udIRqLMfD5Hq9VCOp1GMpnEfD6HzWaD2WymUfWhRzz+YG8qlUKz2YTZbIbf74fb7YZer7/zTjgcDpFMJrFYLODz+aBWq+l41Gg0gsfjgdfrxWw2Q6/Xo0KUyWSQSCTPWrYgMCG+YPhRcDKZoF6vo1QqIZVKoVQqQa1WY29vD36/n96PyIP3EMHwC+vkzlWv1+FyueDz+ahYHvJ6AKgLd7VaRTqdRrPZhNVqxe7uLvx+P33dTwmF79r266+/YjKZ0CJ9Pp9HsViERCKB1+uFxWJBPp9Hp9PBbDaDVquFUql89vohgQnxhXLTzbpWq9H70GAwgMVigdfrRSQSgcViufc98DZItK1Wq4jFYkilUlgul/B4PAiFQg8aH+KLcDgcguM4pNNp1Go1yOVyBINBhEKhO20PbxsOns/n4DgOnU4Hl5eXmEwmcDgccDgcUKlUtCyyXq+h0WhgMBievceUwIT4AiGRcDKZoNFogOM4VKtVNBoNLJdLOJ1OeDweOJ1OmM1myGSyL3YvI4mPWq2Gi4sLRKNRdDodWK1W2nCtUCge9Nr8Yn0mk0GpVIJUKoXb7b4WYT/3mgKBgE5jVCoVNJtNAEC1WkWv10Mmk4HFYqGJHrFYTDuBJBIJtFotFeJzDQPzef53wHgQ5DjG7zqp1+tYr9cwGo2wWCywWq20PnifZMzn/i7imh2LxfDvf/8bFxcXUKlUODw8xNbW1p3F9ZslhtVqhX6/T9vWyuUypFIpgsEgAoEArFYr9R791F4KElVnsxmq1SouLy9RLBYhFosRj8fR7/cxm81wdHREvWuWyyVWqxXtqNFoNNBoNFAqlRthLsWE+IJYrVZYLpfodrs0zV+pVCCXy2Gz2eDz+WA2m6HVaiGTyb76AVuv1xiNRiiXy4jFYkgkEhiPx4hEIjg4OIDL5brTNZtfVgH+FE8ymUS1WoVIJILD4YDb7aaRa7lc0gj+KTHyTwTkSA4AIpEIOp0OTqcTPp8PFosFMpkMg8GArmaTyWRQqVQ0afW5uuRfBRPiC4BfGyQizGazqNVqkEgk8Hg88Pv9sFqttG/yMRIQq9UKrVYLqVQKyWQS7XYbZrMZBwcH2Nragslkutf9iv/+G40GUqkUcrkcRCIRvb+2220MBgMoFAqo1WqaSJFKpbdmeBeLBQaDAWq1GsrlMp2wl0ql8Hg8ODg4oBlUoVCIxWKB2WyG9XoNnU4Hk8n00f2Q9ZoybuVmVrRWqyGbzaJUKmE8HsNsNiMcDsPhcFA7+cdwsuZ35hSLRZydnaFQKEClUuHdu3f47rvv4Ha7oVQq79Vqxk/2XF5eIpVKYTabwWazQSAQIB6Po1AoYDqdQqfT0cZuq9UKk8kEnU4HlUp1zSSYZIlJdrTb7dL/HggE8ObNGzidTshkMlpnHI1GEAgE8Pl82NnZgdFo3BjnbybEDYWfFex2u2g0GigUCuA4DrPZDGazmWYYSa/oY44gkZphJpNBNpvFarWC3++nkYb/d94FmYzP5XIolUoQCoWwWCwQCoUoFotIJBIoFotYLpdQKpUol8swGo0wmUxwu93w+/3XjKFIKSWfz+Pq6gqNRgOLxQIGgwEul4uOSZEkEonG4/GYtve53W5oNJqNKF0ATIgbCbkLEstAjuNQq9XQ6XQgEong8Xjg8XhoWp7ccx5jMp7UDEnmMZFIoNPp0CNpMBik/Zr3ea35fI5+v49CoYBCoYDFYgGXywWFQoFMJoN0Oo1erweTyQSZTEZFk8/n6UanWq2G9XpNSyWz2QyVSoWWUvr9Pm3i3t3dhdfrvdYxQ15zMplgsVhApVJdW0izCTAhbhg3s6Jkvx+525jNZjq2dHM6/bGOpOQYeX5+jnQ6DbFYjK2tLRweHsLpdNJ2s88Jn98EUC6XUSgU0O/3YTab4XQ6MRgM0Gq1MBqN4PV6EQ6HodVqMZlM0G630Wq10Gg00Ov1EIvF6F3R4/Gg3+8jkUjg/PwcuVwO4/EYBoMBHo8HkUgENpvt2mpuvhCXyyUkEglkMtmzzyDyYULcEPg737vdLorFIu2VlEgkMJvN8Hg8tE/0MbKiNxEIBFgul+h0OjQajkYjBINBHB4eIhgMQqfT0UhzV7F9MpmgVCohkUig0WhAqVTSzzCbzSCTyeByuXB4eIjDw0NadO/1euh0OiiXy0ilUiiXy0gmkxCJRJhMJuh2uzg7O6PN4WKxGOFwGMfHxwiHwx91+pB7NsmYkp/dJsGE+MzwBUgGd8lCleFwCJPJBKfTCZvNBpPJBKVS+aiWFPz3AQDT6RSlUgnxeBytVgs+nw//+te/8O7dO9hsNtpUfdfnIcmleDyORCIBo9GIUChE75fr9Rpv3rzBarVCOByG2+2GRCKhoplOpwgEAvB6vYhGo/jjjz/w22+/oVarodvt4sOHD6hUKliv17DZbPjxxx/x008/IRwO07shidrk56tUKuF2u2E2mzduWSkT4jPCz4oOBoNrG43W6zUMBgPtjpHL5Vgul5jP5xAKhXdGpS+BvI9SqQSO42A0GnFycoKjoyMqwrsSNKTtrtPpIJvNolqt0sjn9Xqv2eETURsMBsjlcvq6EokEcrn82nhVtVpFPB6nXTTlchnD4RBarRbBYBD7+/sIBALQ6/UfnRRIIV+n01FHgk0YfeLDhPgM8L+lx+Mxer0eKpUKEokE8vk8lsslTcRMJhPk83ksFguakLDb7XQ06DHeC/DnIG2xWES5XIZQKEQ4HMbJyQlNrnxOhPzeV9L7mclkaLY1FArRLxSBQACNRgOFQgEAH0V4EsmkUikMBgOCwSAODg5Qr9fx66+/IpPJYDweQyqVwmazYWdnh2ZBb4qQ3LkHgwEAwGKxULFuEkyIfxHkQV2tVnSkaDQageM45PN52nO5Xq+h1+uxWq1QqVSoQROpk0UiEbx9+xbhcJjaRnwN/PQ+x3GIx+Oo1+uw2WyIRCJwOp33LlUQC41UKoWLiwu0223qAuBwOK6J+a6oToQpkUhgMBiwu7uLYrGIDx8+UFGpVCq43W5sb2/TKHfbsPBkMgHHcWi327BYLJBKpbRs8ZzdNHyYEJ8YfvSbzWYYDoc0GdFsNmkKn3ht6vV6TCYTNJtNelci/Z6j0QiTyQRKpRJ6vZ72SQJffkT9lIEv2axEjoZ3HUeJE3c+n0cymUSj0aBbe51O560TGvd9z0KhEFKp9FpLmkwmg8PhwNbWFkKh0K2jWDfnKLvdLlar1aNmmh8LJsQngp+06HQ66HQ6aLfbdGawXC6jWq2iUCigUqlgPB5DrVbDaDSi0+lAqVTSArVWq0Wr1cLFxQU4jkMikaB9lOTbndyDSCS57wPG76BJJBJot9twu920AfuuUSTgzwn7TCaDWCyGZrMJk8mEra0tbG9v3znSdBfkeH51dYVOpwMAUCqVcDgcCAaD14rzNyMi6aqZTqcQCASQy+W0frgpIgSYEJ8Mvr/L2dkZ4vE47YlsNptot9vodrvo9/uYTCaQyWRQKpUwm81wuVxwOp20GZp0m3S7XaTTaXQ6HfR6PUynUypAEjklEgmkUimNDndFMv5wbq1Wg16vRyQSoUfJux5WcrfMZrOIxWLU+jASiSAQCMBoND7YvPjm++v3+0in09RkmBxrl8sl9Sm97bXJuFWv18NqtYJSqYRGo6FJp02CCfEJIR0qZAVYqVTCfD6nD6VKpaIlCYPBALfbDZfLRf9pMpmg1WrppAG/eXmxWNBv++FwSEVuNBrhdDqh1+s/WWrg34um0ym1Wuz3+wiHw7QWd1c0XK1WNMsaj8eRyWSgVqtp6x3fIe1LZyHJl0QqlaIu3RaLBUajEYvFAhzHUbcAvi2kQCCgHTjJZBKDwYDOTm5aogZgQnwy+J6iw+EQ0+kUEokEFosFNpsNRqMROp2O/m+TyQS9Xk+dpxUKBRVCv99Hv98Hx3EYDocQiUQQi8WYz+f0If3tt99QqVSwt7eHf/7zn1AoFJ80XOJ7iDabTWqtsV6vaVb2cw3d/EbuYrGIaDSKbDYLAAgGg9jZ2aHjR18zC7lYLFCv12ktstVqQa1W4/j4GDabDd1uF/V6HZeXl3A4HPB4PNdmGSeTCa6urvDbb79hPp8jHA7fuTXquWBCfGRuWlgMBgM6mrO9vU09OskqMFLbUqlU1+p0/IjV7/dRr9dprynprCFHwt9//x3RaJR2xtzlMXpzuoIs6iRfCjdLFTftGvnepplMBuVyGXK5nH5G4gpwU4T3zVDymwtqtRqSySQqlQqEQiG8Xi/+9re/weVyIZ/P03t2sVikNUoS8UgCiSwzvesL5jlhQnxE+LXBer2OWq2GdrsNnU6Ho6Mj2O12eL1eWK1WKJVK+tCQf958cPlHs3K5jMlkAp1OB4PBgOl0ilQqhQ8fPuDi4gLT6RTb29vw+/3XlnZ+CpJJTCaTSKVSkEql8Pl8sNlsH/Vg8vtK1+s1hsMhGo0G0uk0rXv6fD7s7+/D5XJ9MhI+JIFERJhKpXB5eYlerwej0Yj9/X0cHx/T0a/lcklnHE0mE8RiMTQaDf35kd5Sp9MJt9sNlUrFhPiaIUep0WhEH6BWqwUA1LrC6XTCaDTS4V3C546Ag8EA+Xwe+XyedttIJBLkcjkUi0VcXl5iPp/D6/Xi5OQEW1tbd9YXicDJgPFwOITNZqOdL5+KpuSuWqlUqEDI3cvv99PlMF9z9CNfZp1OB4lEAu/fv0cmk4FQKITP58PR0RHC4TAMBgNEIhGq1SqKxSJisRj1KSVNA9PpFLPZDEKhECaTido+biJMiF8JP4Xf6/VQLpeRz+fRbDYhFothMpmuuWrzi8nA50XIb8DmOI5aIVarVepfulwuEQqF8O7dOxwcHHxUOL/tdfk1v0ajAZPJRI2gyET7zT9D7oSVSgV//PEHLi4uMBwO4fF4EAwGabLkZiLkoQVzMv1RLpdxdnaG09NTdDodeDwevH37Fnt7e3RfxXw+h91uh1wuRzabhVgshsFgoObJ/X4fvV4P8/mc1iE3wSjqNjbzXW0wN0d/SNG93W7TyDUYDKjFO2lHIw/BQ3ZMkGQK2Ygkl8tRqVSQy+XQ7/chl8txeHiIf/zjHzg6Oro2h/c5gRMbw0KhgPV6jd3dXbx58wY2m+1azyd/kU2/36f3yffv32MwGCAYDOLo6AihUIh+yQD4oqI9/+fZaDRweXmJ09NT6gzw/fff48cff6SGVUKhEBqNBh6PB4FAAOl0Gqenp1AoFDAYDHA4HGg0Gmg0GhgOh1itVtcyqpsGE+ID4dvbkxocEUsqlcJqtYLD4UA4HIbdbqe7Fb7kuEba4Hq9HprNJoRCIRW0TqfDwcEBfvrpJ+zu7sJms93ZBcMf+iXlCr1eT6cfyAPO/4zT6ZQOCZ+dneH8/ByDwQA+n48OCpNa4dfW5ojoydG3Wq1CLBYjEAjQv4vf1C2Xy+FyubC/v49oNIqLiwvE43GEw2EIhUK02230+/1r9dZNhQnxgfA9RXu93jVfUQBUhB6P59rg7pcUs9frNbWGJzVBrVZL+zdPTk7w9u3bax029yk5kG1Ly+WSWtvz153xk04cxyGbzeL8/BwXFxfo9/vw+/04Pj7Gzs4OTQw9hgiBP8ew0uk0RqMRHA4HtW4kgieIRCLo9XqEQiHs7++j2Wyi1WohGo0CAEajEcbjMZbL5fM8LA+ACfEBkChIjnYcx6HRaGA8HkOpVCISicBut1/bsPSlxyDS8Gw0GrG9vQ2hUAi1Wg2HwwGfz4dAIACPx3PvtWIkE8lxHC4uLpDL5WAymbC7uwu73U5fg3zJkMHc8/NzxONxWmf0+/344YcfsL+//1ED9WP8fMkC1EqlAo1Gg+PjY3z//ffw+XwfdfqQHlSr1Yrvv/8e0+kUv/zyC6LRKLrdLjQaDbVR3HSYEO8Jf+01cVPrdrs0I2ez2T6KLF9zFyFCtNvtODk5odPxJpMJRqMRBoOB7rK4jwjJkZS4wAkEAjgcDjpdIRAI6H2X+I4mEgnE43E0m00olUoEg0EcHx9jb28PFovl0fdGkNnMxWIBpVIJl8uFk5MThMPhT67qFgqFUCgUtHxSKpVwenqKWCwGk8lEF85s8rEUYEK8E37xu16vo1qtguM4mizhd8ZotdpHESF5aEjWdW9vjz6cxOvztsTPpxZ3kkhD9kz0ej243W565xKLxfQuyI+CxWIRk8kEBoMBW1tbODg4oCNHT2W8RGYu9/b2sLW1hf39fVit1ltnL8nnJdnSQCCAvb095HI5mtCaz+cwmUxP8Wg8KkyIn4A/vsQ3xi0UCnS5icfjgdVq/aKs6Ofgj+iQVrf1en2ta+RTrWu3QcogZOhXKpUiHA4jEAhALpfTz0f2E75//x6NRgNSqZSaRm1vb9M9Ek/VNE12Fm5vb1MrRX4R/lMNAiRx43Q68ebNG2SzWfR6PdRqNYxGI+j1+o2btrgJE+ItEAGSyW7+uNJyuYTNZkMwGKRZ0ce8J92E3IP4I073PWbx2+3I+BUA+Hw+eL1eaDQa2iVDiufkKKrX67G1tYW///3v2NnZgc1mozaKT/VAky8e0hyg1Wrv3K1B/hxp/QuFQvjxxx8hlUoRjUZRrVZhsViutbZtyjAwHyZEHvwHlwzvchyHYrGIdrsNtVqNUCiEQCBAkyRP4R3D57YB1of8XbPZDO12m5r7qtVq+P1+yGQyWktMpVKIx+PI5XJYrVaIRCLY39/HwcEBIpEIteV4yjoc36/GbDbTjPF9neoEAgGkUiksFgt++OEHuN1ubG1toVAoQK1W0z0dT/m7+hqYEP8//GNot9tFoVBAuVxGs9nEfD6HXq+/lqncJHPaz30m4kOTzWbBcRzcbjdmsxny+Tw4jkMsFkM+n8dwOIRGo0EgEMDbt28RCoXgcrnutSLtsSAR/0tOF+T9icViWubQarXo9XqQSCRwOBwb59zGhwkRf3axkLEakj4fjUYQiUSw2+10d59Wq71XuWATIEtkEokELi4uaK1TKBRiNpuhVquhVqsBAFwuF7a3t7Gzs0MnKMi99yV8VgIRslqtps53JAO9iXOIhG9aiPyjaLvdRiqVQjabpYkKsiPB4XBcW2r5uXVhmwKJ8I1Gg2ZByXGbjBSJxWJYrVaEQiE6UW+1WqHVajc6etwFuTPettptU39v36QQSbKDFK/b7Tay2SzOzs5oIZh0nNhsto/uSMBm3jNu+5yk9jkcDjEej6krGonyW1tbNDmi1+vv7FV9SXzt/fqv5JsTIr+RmV+cz+Vy6HQ60Ol0CIVCdP8ff1Ie2Nxf5G0QOw5y7JzNZnTSgmRO3W439Ho95HI57Yll/PUI1pvecvCIkOI2sSckzcWkr9Hn82F7e5tu3n2K/RJ/JYvFgs7qkTEqs9lMo59araZ7BzfRPuJb4psQIn9mcDweo9VqIZvN0iUv0+kUJpMJR0dH8Pv91HiJXwTe1LvF51itVhgOh2g2m5hMJtd2x5O77iaPBn1LvHoh8ne3N5tNVKtVlEolFAoFjEYjWmMKBAK0i+Muz5eXAn9ci3yR3LYGm/H8vHohEoftdruNZDKJbDaLZrMJkUhEV52RDUHEOfu1PKREfORX/Fo+12vkVQjxtvQ0iQZkv8TV1RWSySSGwyFUKhUVIDEhIl0y7GFlPAevQog34TtYky1LiUQCw+EQVqsV29vb1IX6qfYNMhgP4VWVL0hpgjRrExGm02mMx2P4fD5sbW3B6/XSne2b3G3B+HZ4NRGRb2HRarWQyWSQyWRQqVQAgLqAuVwu6ikKsHsTYzN4FRGRREEy0pPNZunqaaVSCb/fj0gkQrOiLGvI2DRevBBJgb7b7aJYLCKTyaBQKKDf70On08Hn81GXMuJyxmBsGi9WiHwLi3K5TJ2nOY6DWCym66LJffA19VAyXh8vUoj8+2Cj0UA0GsXp6SlqtRqUSiVCoRAODw/hcrmg0+muLXdhMDaRFyVE/h56YmmYSCRwdnZG96OHw2FEIhH4/f5HM3NiMJ6ajRXizW4QfsP2cDhEpVLB5eUlYrEYWq0WrFYrjo+PEYlE6PLPT2VGX2LfKON1s/HlC35tcDKZoNvtolKpUJPcwWAAm82G3d1d7OzswOFwfNWWWgbjOdhoIfKPoe12mzZrEy8ZkUgEp9OJw8NDBAIBOjVBDGWZEBkvhY09mpKpAWJ+RJa8lMtlLBYLWCwWBAIBhMNh+P1+6HQ6NtjKeLFsnBD5q6E7nQ5yuRzev3+PVCqFdrsNuVxO7dV9Ph9de3bbllsG46WwEULke8iQrbuFQgHJZBLRaBSJRALT6RQOhwP7+/t0Jx9xVGP9ooyXzrMI8ebYEknGjMdjurvv999/RzQaRbFYhFAohN/vp1txnU7nR1HwIQ7YDMam8azJGv4yzG63S/f2XV1d4fLyEp1OBzKZDNvb2zg8PMTu7i5cLhfdQc+On4zXwrMJkb8Ms16vI5/P4+LiAolEAuVyGev1GmazGeFwGG/fvkUwGKStaqxLhvHa+EuFyJ+eJ3aG1WoVsVgMFxcXyOfz6Pf7UCqVCAQCdM+Ez+eDwWBgbmOMV8uTC/Gmbwp/c+3V1RXi8Tii0Sg6nQ5UKhXd2xeJRKjtH/GSAVg2lPE6efJkDd8/ZjabYTQaURv4aDRKfWQsFgsODg6wv78Pt9sNi8VCzX1ZFGS8dh5NiDc7WfgRcD6fYzweo9lsIpfLIR6P4/T0FNVqFQKBANvb27RP1OVyQaPRXJuYYCJkvHYeTYi3iYUkY8iOwcvLS5yfnyOZTKLX68FkMmF3dxf/93//h4ODAxiNxmsRkAmQ8a3wJEdT/t72SqWCs7MzRKNRxGIxFItFLBYLeDweHB0d4d27d9jb26OLP5n4GN8ijy5Evp9otVrF6ekpfv75Z1xeXqLZbEIikSAUCuG7776jHTJms5luc+W/DhMl41vhUYXIt68oFouIRqP45ZdfcH5+jsFgAIPBgGAwiMPDQ5ycnMDr9dIJ+ptHUSZCxrfEo0fE5XKJfr+PaDSKn3/+Gel0GiKRCJFIBJFIBLu7uwgEAjQpw8x9GYwnEuJkMkGz2cRoNILX60U4HEYwGEQgEIDD4YBaraZZUYBFPwbj0YUoFAohkUhgt9txdHQEl8uF3d1dGAwGKkDipsYEyGD8j0ftrOEnaiqVCobDIYxGI136ebMwz4TIYPyPRxci8Odc4Wq1gkgkYk5qDMYdPEmv6c2XZAJkMD7PkxT0mfAYjIfBnJYYjA2ACZHB2ACYEBmMDYAJkcHYAJgQGYwNgAmRwdgAmBAZjA2ACZHB2ACYEBmMDeD/ARrS1/Lfpl13AAAAEnRFWHRFWElGOk9yaWVudGF0aW9uADGEWOzvAAAAAElFTkSuQmCC',
            ],

        /*
        |--------------------------------------------------------------------------
        | Si la entidad certificadora se encuentra en el registro Europeo
        | UE 910/2014 de Terceros de Confianza Digital
        |--------------------------------------------------------------------------
        */
        'ue_910_2014'  => true,
    ];
