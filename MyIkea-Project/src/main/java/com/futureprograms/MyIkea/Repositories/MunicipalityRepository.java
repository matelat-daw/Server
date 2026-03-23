package com.futureprograms.MyIkea.Repositories;

import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.stereotype.Repository;
import com.futureprograms.MyIkea.Models.Municipality;
import com.futureprograms.MyIkea.Models.Province;

import java.util.List;

@Repository
public interface MunicipalityRepository extends JpaRepository<Municipality, Integer> {
    List<Municipality> findByProvincia(Province provincia);
    List<Municipality> findByProvinciaIdProvincia(Integer idProvincia);
}