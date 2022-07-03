package com.accelerate.acceleronServices.reservation.service;

import com.accelerate.acceleronServices.reservation.dto.request.ReservationDto;
import com.accelerate.acceleronServices.reservation.dto.response.ApiResponse;
import com.accelerate.acceleronServices.reservation.dto.response.GenericResponse;
import com.accelerate.acceleronServices.reservation.enums.StatusTextEnum;
import com.accelerate.acceleronServices.reservation.model.ReservationEntity;
import com.accelerate.acceleronServices.reservation.repository.ReservationRepository;
import lombok.AllArgsConstructor;
import lombok.extern.slf4j.Slf4j;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.http.HttpStatus;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;

import java.util.List;

@Service
@AllArgsConstructor
@Slf4j
public class ReservationServiceImpl implements ReservationService {

    @Autowired
    private ReservationRepository reservationRepository;

    @Override
    @Transactional
    public ApiResponse<GenericResponse> makeReservation(ReservationDto request) {
        reservationRepository.makeReservation(request.getName(), request.getMobileNo());

        ApiResponse<GenericResponse> response = new ApiResponse<>();
        response.setStatus(true);
        response.setMessage(StatusTextEnum.SUCCESS.value());
        response.setStatusCode(HttpStatus.OK.value());
        response.setData(new GenericResponse(StatusTextEnum.SUCCESS.value()));
        return response;
    }



    @Override
    public List<ReservationEntity> getReservation(String name) {
        return reservationRepository.findByName(name);
    }
    @Override
    public List<ReservationEntity> getAllReservation(){
        return reservationRepository.findAll();
    }

    @Override
    public ApiResponse<GenericResponse> deleteReservation(String name) {
        reservationRepository.deleteByName(name);


        ApiResponse<GenericResponse> response = new ApiResponse<>();
        response.setStatus(true);
        response.setMessage(StatusTextEnum.SUCCESS.value());
        response.setStatusCode(HttpStatus.OK.value());
        response.setData(new GenericResponse(StatusTextEnum.SUCCESS.value()));
        return response;
    }

    @Override
    public ApiResponse<GenericResponse> updateReservation(ReservationDto request) {
        reservationRepository.updateReservation(request.getName(), request.getMobileNo());

        ApiResponse<GenericResponse> response = new ApiResponse<>();
        response.setStatus(true);
        response.setMessage(StatusTextEnum.SUCCESS.value());
        response.setStatusCode(HttpStatus.OK.value());
        response.setData(new GenericResponse(StatusTextEnum.SUCCESS.value()));
        return response;
    }
}
