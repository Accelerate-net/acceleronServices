package com.accelerate.acceleronServices.reservation.utils;

import com.accelerate.acceleronServices.reservation.dto.request.ReservationRequestDto;
import com.accelerate.acceleronServices.reservation.model.ReservationEntity;
import org.modelmapper.ModelMapper;
import org.modelmapper.TypeToken;
import org.springframework.beans.factory.annotation.Autowired;

import java.lang.reflect.Type;

public class EntityDtoConversion {

    @Autowired
    private ModelMapper modelMapper;

    public <T1, T2> T1 convert(T2 dto){
        Type typeOfT1 = new TypeToken<T1>(){}.getType();
        return modelMapper.map(dto, typeOfT1);
    }

    public <T> ReservationEntity convertToEntity(T entity){
        return modelMapper.map(entity, ReservationEntity.class);
    }

    public <T> ReservationRequestDto convertToDto(T entity){
        return modelMapper.map(entity, ReservationRequestDto.class);
    }
}
