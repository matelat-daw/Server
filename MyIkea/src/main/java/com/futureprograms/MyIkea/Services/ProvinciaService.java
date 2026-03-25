package com.futureprograms.MyIkea.Services;

import org.springframework.stereotype.Service;
import com.futureprograms.MyIkea.Models.Province;
import com.futureprograms.MyIkea.Repositories.ProvinceRepository;

import java.util.List;

@Service
public class ProvinciaService {
    private final ProvinceRepository provinceRepository;

    public ProvinciaService(ProvinceRepository provinceRepository) {
        this.provinceRepository = provinceRepository;
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
