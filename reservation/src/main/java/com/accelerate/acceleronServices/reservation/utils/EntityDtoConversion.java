package com.accelerate.acceleronServices.reservation.utils;

import com.accelerate.acceleronServices.reservation.dto.request.ReservationRequestDto;
import com.accelerate.acceleronServices.reservation.model.ReservationEntity;
import org.modelmapper.ModelMapper;
import org.springframework.beans.factory.annotation.Autowired;

public class EntityDtoConversion {

    @Autowired
    private ModelMapper modelMapper;

    public <T> ReservationEntity convertToEntity(T dto){

        return modelMapper.map(dto, ReservationEntity.class);
    }

    public <T> ReservationRequestDto convertToDto(T entity){
        return modelMapper.map(entity, ReservationRequestDto.class);
    }
}
