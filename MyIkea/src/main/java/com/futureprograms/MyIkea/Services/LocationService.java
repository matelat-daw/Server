package com.futureprograms.MyIkea.Services;

import org.springframework.stereotype.Service;
import com.futureprograms.MyIkea.Models.Municipality;
import com.futureprograms.MyIkea.Models.Province;
import com.futureprograms.MyIkea.Repositories.MunicipalityRepository;
import com.futureprograms.MyIkea.Repositories.ProvinceRepository;

import java.util.List;

@Service
public class LocationService {
    private final MunicipalityRepository municipalityRepository;
    private final ProvinceRepository provinceRepository;

    public LocationService(MunicipalityRepository municipalityRepository, ProvinceRepository provinceRepository) {
        this.municipalityRepository = municipalityRepository;
        this.provinceRepository = provinceRepository;
    }

    public List<Municipality> getAllMunicipios() {
        return municipalityRepository.findAll();
    }

    public Municipality getMunicipioById(Integer id) {
        return municipalityRepository.findById(id).orElse(null);
    }

    public List<Municipality> getMunicipiosByProvincia(Province provincia) {
        return municipalityRepository.findByProvincia(provincia);
    }

    public List<Municipality> getMunicipiosByProvinciaId(Integer idProvincia) {
        return municipalityRepository.findByProvinciaIdProvincia(idProvincia);
    }

    public Municipality saveMunicipio(Municipality municipio) {
        return municipalityRepository.save(municipio);
    }

    public void deleteMunicipio(Integer id) {
        municipalityRepository.deleteById(id);
    }

    public List<Province> getAllProvincias() {
        return provinceRepository.findAll();
    }

    public Province getProvinciaById(Integer id) {
        return provinceRepository.findById(id).orElse(null);
    }

    public Province saveProvincia(Province provincia) {
        return provinceRepository.save(provincia);
    }

    public void deleteProvincia(Integer id) {
        provinceRepository.deleteById(id);
    }
}
