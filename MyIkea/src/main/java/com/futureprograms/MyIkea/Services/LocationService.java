package com.futureprograms.MyIkea.Services;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Service;
import com.futureprograms.MyIkea.Models.Municipality;
import com.futureprograms.MyIkea.Models.Province;
import com.futureprograms.MyIkea.Repositories.MunicipalityRepository;
import com.futureprograms.MyIkea.Repositories.ProvinceRepository;

import java.util.List;

@Service
public class LocationService {
    @Autowired
    private MunicipalityRepository Mr;
    @Autowired
    private ProvinceRepository provinciaRepository;

    public List<Municipality> getAllMunicipios() {
        return Mr.findAll();
    }

    public Municipality getMunicipioById(Integer id) {
        return Mr.findById(id).orElse(null);
    }

    public List<Municipality> getMunicipiosByProvincia(Province provincia) {
        return Mr.findByProvincia(provincia);
    }

    public Municipality saveMunicipio(Municipality municipio) {
        return Mr.save(municipio);
    }

    public void deleteMunicipio(Integer id) {
        Mr.deleteById(id);
    }

    public List<Municipality> getMunicipiosByProvincia(Integer idProvincia) {
        return Mr.findByProvinciaIdProvincia(idProvincia);
    }

    public List<Province> getAllProvincias() {
        return provinciaRepository.findAll();
    }
}
