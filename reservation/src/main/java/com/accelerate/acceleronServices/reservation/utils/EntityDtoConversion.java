package com.accelerate.acceleronServices.reservation.utils;

import com.accelerate.acceleronServices.reservation.dto.request.ReservationRequestDto;
import com.accelerate.acceleronServices.reservation.model.ReservationEntity;
import org.modelmapper.ModelMapper;
import org.springframework.beans.factory.annotation.Autowired;

public class EntityDtoConversion {

    @Autowired
    private ModelMapper modelMapper;

    public ReservationEntity convertToEntity(ReservationRequestDto reservationRequestDto){
        return modelMapper.map(reservationRequestDto, ReservationEntity.class);
    }

    public ReservationRequestDto convertToDto(ReservationEntity reservationEntity){
        return modelMapper.map(reservationEntity, ReservationRequestDto.class);
    }
}
