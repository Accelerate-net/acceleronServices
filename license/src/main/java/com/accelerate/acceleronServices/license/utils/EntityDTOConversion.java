package com.accelerate.acceleronServices.license.utils;

import com.accelerate.acceleronServices.license.dto.request.LicenseRequestDto;
import com.accelerate.acceleronServices.license.model.LicenseEntity;
import org.modelmapper.ModelMapper;
import org.modelmapper.TypeToken;
import org.springframework.beans.factory.annotation.Autowired;

import java.lang.reflect.Type;

public class EntityDTOConversion {

    @Autowired
    private ModelMapper modelMapper;

    public <T1, T2> T1 convert(T2 dto){
        Type typeOfT1 = new TypeToken<T1>(){}.getType();
        return modelMapper.map(dto, typeOfT1);
    }

    public <T> LicenseEntity convertToEntity(T entity){
        return modelMapper.map(entity, LicenseEntity.class);
    }

    public <T> LicenseRequestDto convertToDto(T entity){
        return modelMapper.map(entity, LicenseRequestDto.class);
    }
}
