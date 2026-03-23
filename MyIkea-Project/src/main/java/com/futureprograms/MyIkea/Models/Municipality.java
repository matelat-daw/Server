package com.futureprograms.MyIkea.Models;

import jakarta.persistence.*;
import lombok.Data;

@Entity
@Data
@Table(name = "municipios")
public class Municipality {
    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    @Column(name = "id_municipio")
    private Integer idMunicipio;

    @ManyToOne(optional = false, fetch = FetchType.LAZY)
    @JoinColumn(name = "id_provincia", referencedColumnName = "id_provincia", nullable = false)
    private Province provincia;

    @Column(nullable = false, name = "cod_municipio")
    private Integer codMunicipio;

    @Column(nullable = false, name = "DC")
    private Integer dc;

    @Column(nullable = false, length = 100, name = "nombre")
    private String nombre;

    public Integer getIdMunicipio() {
        return idMunicipio;
    }

    public void setIdMunicipio(Integer idMunicipio) {
        this.idMunicipio = idMunicipio;
    }

    public Province getProvincia() {
        return provincia;
    }

    public void setProvincia(Province provincia) {
        this.provincia = provincia;
    }

    public Integer getCodMunicipio() {
        return codMunicipio;
    }

    public void setCodMunicipio(Integer codMunicipio) {
        this.codMunicipio = codMunicipio;
    }

    public Integer getDc() {
        return dc;
    }

    public void setDc(Integer dc) {
        this.dc = dc;
    }

    public String getNombre() {
        return nombre;
    }

    public void setNombre(String nombre) {
        this.nombre = nombre;
    }
}
