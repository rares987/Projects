package Repository;

import DataBaseAccess.DBAccess;
import Entity.CameraEntity;
import Entity.CaminEntity;
import Entity.StudentEntity;

import javax.persistence.EntityManager;
import javax.persistence.Query;
import java.math.BigInteger;
import java.util.ArrayList;
import java.util.Iterator;
import java.util.List;

public class RepositoryStudent implements Repository<StudentEntity>{

    private EntityManager em = DBAccess.getInstance();

    private RepositoryCamera localCameraRepository = new RepositoryCamera();

    @Override
    public int count() {
        Query countQuery = em.createNativeQuery("SELECT count(*) FROM student");
        Object result = countQuery.getSingleResult();
        return ((BigInteger) result).intValue();
    }

    @Override
    public void deleteAll() {
        Query deleteQuery = em.createNativeQuery("DELETE from student");
        deleteQuery.executeUpdate();
    }

    @Override
    public void deleteById(int id) {
        Query deleteQuery = em.createNativeQuery("DELETE from student where id = ?1");
        deleteQuery.setParameter(1,id);
        deleteQuery.executeUpdate();
    }

    @Override
    public boolean existsById(int id) {
        Query existsQuery = em.createNativeQuery("select count(*) FROM student WHERE id = ?1");
        existsQuery.setParameter(1,id);
        Object result = existsQuery.getSingleResult();
        if ( ((Integer) result).intValue() == 0){
            return false;
        }
        return true;
    }
    @Override
    public List<StudentEntity> findAll() {
        ArrayList<StudentEntity> resultList = new ArrayList<>();
        Query findQuery = em.createNativeQuery("SELECT * FROM student");

        List<Object> result = (List<Object>) findQuery.getResultList();
        Iterator itr = result.iterator();

        while (itr.hasNext()) {
            StudentEntity student = new StudentEntity();
            Object[] obj = (Object[]) itr.next();
            Integer id = Integer.parseInt(String.valueOf(obj[0]));
            if (obj[1] != null) {
                student.setReferencedCamera(localCameraRepository.findById(Integer.parseInt(String.valueOf(obj[1]))));
                student.setIdCamera(student.getReferencedCamera().getId());
            }
            String nume = String.valueOf(obj[2]);
            String sex = String.valueOf(obj[3]);
            String nationalitate = String.valueOf(obj[4]);
            Float medie = Float.valueOf(String.valueOf(obj[5]));
            String prenume = String.valueOf(obj[6]);
            student.setId(id);
            student.setNume(nume);
            student.setPrenume(prenume);
            student.setSex(sex);
            student.setNationalitate(nationalitate);
            student.setMedie(medie);
            resultList.add(student);
        }
        return resultList;
    }

    public List<StudentEntity> findAllF() {
        ArrayList<StudentEntity> resultList = new ArrayList<>();
        Query findQuery = em.createNativeQuery("SELECT * FROM student where sex = \'female\'");

        List<Object> result = (List<Object>) findQuery.getResultList();
        Iterator itr = result.iterator();

        while (itr.hasNext()) {
            StudentEntity student = new StudentEntity();
            Object[] obj = (Object[]) itr.next();
            Integer id = Integer.parseInt(String.valueOf(obj[0]));
            if (obj[1] != null) {
                student.setReferencedCamera(localCameraRepository.findById(Integer.parseInt(String.valueOf(obj[1]))));
                student.setIdCamera(student.getReferencedCamera().getId());
            }
            String nume = String.valueOf(obj[2]);
            String sex = String.valueOf(obj[3]);
            String nationalitate = String.valueOf(obj[4]);
            Float medie = Float.valueOf(String.valueOf(obj[5]));
            String prenume = String.valueOf(obj[6]);
            student.setId(id);
            student.setNume(nume);
            student.setPrenume(prenume);
            student.setSex(sex);
            student.setNationalitate(nationalitate);
            student.setMedie(medie);
            resultList.add(student);
        }
        return resultList;
    }

    public List<StudentEntity> findAllM() {
        ArrayList<StudentEntity> resultList = new ArrayList<>();
        Query findQuery = em.createNativeQuery("SELECT * FROM student where sex = \'male\'");

        List<Object> result = (List<Object>) findQuery.getResultList();
        Iterator itr = result.iterator();

        while (itr.hasNext()) {
            StudentEntity student = new StudentEntity();
            Object[] obj = (Object[]) itr.next();
            Integer id = Integer.parseInt(String.valueOf(obj[0]));
            if (obj[1] != null) {
                student.setReferencedCamera(localCameraRepository.findById(Integer.parseInt(String.valueOf(obj[1]))));
                student.setIdCamera(student.getReferencedCamera().getId());
            }
            String nume = String.valueOf(obj[2]);
            String sex = String.valueOf(obj[3]);
            String nationalitate = String.valueOf(obj[4]);
            Float medie = Float.valueOf(String.valueOf(obj[5]));
            String prenume = String.valueOf(obj[6]);
            student.setId(id);
            student.setNume(nume);
            student.setPrenume(prenume);
            student.setSex(sex);
            student.setNationalitate(nationalitate);
            student.setMedie(medie);
            resultList.add(student);
        }
        return resultList;
    }

    @Override
    public StudentEntity findById(int queryId) {
        StudentEntity returnStudentEntity = new StudentEntity();
        Integer idCamera=null;
        Query existsQuery = em.createNativeQuery("select id FROM student WHERE id = ?1");
        existsQuery.setParameter(1, queryId);
        Object result = existsQuery.getSingleResult();
        Integer id = ((Number) result).intValue();

        existsQuery = em.createNativeQuery("select id_camera FROM student WHERE id = ?1");
        existsQuery.setParameter(1, queryId);
        result = existsQuery.getSingleResult();
        if (result == null) {
            idCamera = 0;
        }
        else {
            idCamera = ((Number) result).intValue();
        }

        existsQuery = em.createNativeQuery("select nume FROM student WHERE id = ?1");
        existsQuery.setParameter(1, queryId);
        result = existsQuery.getSingleResult();
        String nume = result.toString();

        existsQuery = em.createNativeQuery("select prenume FROM student WHERE id = ?1");
        existsQuery.setParameter(1, queryId);
        result = existsQuery.getSingleResult();
        String prenume = result.toString();

        existsQuery = em.createNativeQuery("select sex FROM student WHERE id = ?1");
        existsQuery.setParameter(1, queryId);
        result = existsQuery.getSingleResult();
        String sex = result.toString();

        existsQuery = em.createNativeQuery("select nationalitate FROM student WHERE id = ?1");
        existsQuery.setParameter(1, queryId);
        result = existsQuery.getSingleResult();
        String nationalitate = result.toString();

        existsQuery = em.createNativeQuery("select medie FROM student WHERE id = ?1");
        existsQuery.setParameter(1, queryId);
        result = existsQuery.getSingleResult();
        Float medie = ((Float) result).floatValue();

        returnStudentEntity.setId(id);
        returnStudentEntity.setIdCamera(idCamera);
        if (!idCamera.equals(0))
        {
            returnStudentEntity.setReferencedCamera(localCameraRepository.findById(idCamera));
        }
        returnStudentEntity.setMedie(medie);
        returnStudentEntity.setNationalitate(nationalitate);
        returnStudentEntity.setNume(nume);
        returnStudentEntity.setSex(sex);
        returnStudentEntity.setPrenume(prenume);
        returnStudentEntity.setMedie(medie);

        return returnStudentEntity;
    }

    @Override
    public void save(StudentEntity student) {
            em.persist(student);
    }
}
